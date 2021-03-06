#!/bin/bash

# kcp 
chmod 755 plugin/kcpcert/bin/ct_cli
chmod 755 plugin/kcpcert/bin/ct_cli_x64

# okname 
chmod 755 plugin/okname/bin/okname
chmod 755 plugin/okname/bin/okname_x64

# kcp 
if [ -d "shop" ]; then
  chmod 755 shop/kcp/bin/pp_cli
  chmod 755 shop/kcp/bin/pp_cli_x64
fi

echo "Complete change permissions."
