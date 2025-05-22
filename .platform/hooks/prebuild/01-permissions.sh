#!/bin/bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:$USER storage bootstrap/cache 