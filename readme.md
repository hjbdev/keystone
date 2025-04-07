# Keystone

Keystone is an opinionated Laravel deployment tool. Think of it as a middle-ground between Forge and Cloud, with Envoyer built in.

## STUFF

MAKE SURE TO INSTALL sshpass on the server this is running on

## Overview

Every application has a gateway (just a load balancer), regardless of how many app servers it's running.
We're going to install wireguard on each server to provide a secure connection between every server and manage internal connections via the firewall with ufw.
For each server provider, we should create a private network on that provider to get the lowest latency, which means allocating the wireguard connections needs to be done intelligently. If the server provider is not the same, we should use the public IP, otherwise use the private one internally.
If a server is created on a provider, we should create the 'keystone' network. Maybe search to see if it already exists first.

