#!/bin/bash
# [hostname!] - server hostname
# [sudo_password!] - the sudo password to set
# [server_id!] - the servers id
# [keystonepublickey!] - keystone's public key
# [callback!] - callback url

apt_wait() {
    while fuser /var/lib/dpkg/lock >/dev/null 2>&1; do
        echo "Waiting: dpkg/lock is locked..."
        sleep 5
    done
    while fuser /var/lib/dpkg/lock-frontend >/dev/null 2>&1; do
        echo "Waiting: dpkg/lock-frontend is locked..."
        sleep 5
    done
    while fuser /var/lib/apt/lists/lock >/dev/null 2>&1; do
        echo "Waiting: lists/lock is locked..."
        sleep 5
    done
    if [ -f /var/log/unattended-upgrades/unattended-upgrades.log ]; then
        while fuser /var/log/unattended-upgrades/unattended-upgrades.log >/dev/null 2>&1; do
            echo "Waiting: unattended-upgrades is locked..."
            sleep 5
        done
    fi
}

apt_wait

# Make sure we're up to date
export DEBIAN_FRONTEND=noninteractive
apt update
apt_wait
apt upgrade -y
apt_wait
apt install unzip curl fail2ban ufw -y

# No password logins
sed -i "/PasswordAuthentication yes/d" /etc/ssh/sshd_config
echo "" | sudo tee -a /etc/ssh/sshd_config
echo "" | sudo tee -a /etc/ssh/sshd_config
echo "PasswordAuthentication no" | sudo tee -a /etc/ssh/sshd_config

# UTC
ln -sf /usr/share/zoneinfo/UTC /etc/localtime

# Create The Root SSH Directory If Necessary
if [ ! -d /root/.ssh ]; then
    mkdir -p /root/.ssh
    touch /root/.ssh/authorized_keys
fi

# Set The Hostname If Necessary
echo "[!hostname!]" > /etc/hostname sed -i 's/127\.0\.0\.1.*localhost/127.0.0.1 [!hostname!].localdomain [!hostname!] localhost/' /etc/hosts
hostname [!hostname!]

# Setup Keystone User
useradd keystone
mkdir -p /home/keystone/.ssh
mkdir -p /home/keystone/.keystone
adduser keystone sudo

# Setup Bash For Keystone User
chsh -s /bin/bash keystone
cp /root/.profile /home/keystone/.profile
cp /root/.bashrc /home/keystone/.bashrc

# Set The Sudo Password For Keystone
PASSWORD=$(mkpasswd [!sudo_password!])
usermod --password $PASSWORD keystone

# Build Formatted Keys & Copy Keys To Keystone
cat >/root/.ssh/authorized_keys <<EOF
# Keystone
[!keystonepublickey!]
EOF

cp /root/.ssh/authorized_keys /home/keystone/.ssh/authorized_keys

# Create The Server SSH Key
ssh-keygen -f /home/keystone/.ssh/id_ed25519 -t ed25519 -N ''

# Restart SSH
service ssh restart

# Setup Keystone Home Directory Permissions
chown -R keystone:keystone /home/keystone
chmod -R 755 /home/keystone
chmod 700 /home/keystone/.ssh/id_rsa

# Setup UFW Firewall
ufw allow 22
# ufw allow 80 # only if web
# ufw allow 443
ufw --force enable

# Add Keystone User To www-data Group
usermod -a -G www-data keystone
id keystone
groups keystone

# Install bun and pm2
curl -fsSL https://bun.sh/install | bash
source $HOME/.bashrc
bun add -g pm2
ln -s $(which bun) /usr/bin/node # (official) workaround for not having node
$(pm2 startup | sed 's/^sudo //') | su - keystone -c "bash"

# Install Docker Engine
apt_wait
apt-get -y remove docker docker-engine docker.io containerd runc
apt_wait
apt-get update
apt_wait
apt-get -y install \
    ca-certificates \
    curl \
    gnupg \
    lsb-release
apt_wait

curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null

apt-get update
apt_wait
apt-get -y install docker-ce docker-ce-cli containerd.io
apt_wait
# end docker install

# Setup Unattended Security Upgrades
apt-get install -y --force-yes unattended-upgrades
cat >/etc/apt/apt.conf.d/50unattended-upgrades <<EOF
Unattended-Upgrade::Allowed-Origins {
    "Ubuntu focal-security";
};
Unattended-Upgrade::Package-Blacklist {
    //
};
EOF

cat >/etc/apt/apt.conf.d/10periodic <<EOF
APT::Periodic::Update-Package-Lists "1";
APT::Periodic::Download-Upgradeable-Packages "1";
APT::Periodic::AutocleanInterval "7";
APT::Periodic::Unattended-Upgrade "1";
EOF


# Callback that the server is installed
curl --insecure --data "server_id=[!server_id!]" [!callback!]