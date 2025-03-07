#!/bin/sh -eu

debInst() {
    dpkg-query -Wf'${db:Status-abbrev}' "$1" 2>/dev/null | grep -q '^i'
}

case $PUPPET_VERSION in
    ''|*[!0-9]*)
        echo "The given Puppet version is not valid: $PUPPET_VERSION"
        exit 1
        ;;
    *)
        echo "Installing Puppet Version: $PUPPET_VERSION"
        ;;
esac

PACKAGE_NAME="puppet$PUPPET_VERSION-release"
#DIST_NAME="$(/usr/bin/lsb_release --codename --short)"
DIST_NAME="focal"

if debInst "$PACKAGE_NAME"; then
    echo "Puppet is already installed, skipping..."
else
    /usr/bin/curl https://apt.puppet.com/$PACKAGE_NAME-$DIST_NAME.deb --output /tmp/$PACKAGE_NAME.deb
    /usr/bin/sudo /usr/bin/dpkg -i /tmp/$PACKAGE_NAME.deb
    sudo DEBIAN_FRONTEND="noninteractive" apt-get -y update
    sudo DEBIAN_FRONTEND="noninteractive" apt-get -y install puppet-agent
    rm /tmp/$PACKAGE_NAME.deb

    cat <<"EOF" > /usr/share/oh-my-zsh/custom/80-add-puppet-path.zsh
# Add /opt/puppetlabs/bin to the path for zsh compatible users
export PATH="${PATH}:/opt/puppetlabs/bin"
export MANPATH="${MANPATH}:/opt/puppetlabs/puppet/share/man"
EOF

    # add puppet bin directory to the sudo secure path
    SECURE_PATH="Defaults secure_path=\"/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/snap/bin:/opt/puppetlabs/bin\""
    echo $SECURE_PATH > /etc/sudoers.d/secure-path
fi
