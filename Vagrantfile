#https://github.com/hashicorp/vagrant/issues/8878#issuecomment-345112810
class VagrantPlugins::ProviderVirtualBox::Action::Network
  def dhcp_server_matches_config?(dhcp_server, config)
    true
  end
end

require_relative ".devsupport/vagrant/project"
require_relative ".devsupport/vagrant/util"

require_admin_mode()

# Check & install plugins
require_plugins(['r10k', 'vagrant-hostmanager-rethinc'])

# Parse the project definition
project = Project.new('project.yaml')
project.write_project_hiera('.devsupport/puppet/project-data.yaml')

project_root = File.dirname(__FILE__)

Vagrant.configure(2) do |config|
  # Download our puppet profile before machine start and provisioning
#  config.trigger.before [:up, :provision] do |t|
     require_relative ".devsupport/vagrant/util_fetch"
#    t.name = "fetch-puppet-deps"
#    t.info = "Fetching puppet dependencies."
#    t.ruby do |env,machine|
#      fetch_puppet_dependencies(project_root + '/.devsupport/puppet/localdev')
#    end
#  end

  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.manage_guest = true
  config.hostmanager.ip_resolver = proc do |vm, resolving_vm|
    read_ip_address(vm)
  end

  # Define the machines
  project.machines.each do |machine|
    config.vm.define machine['name'] do |machine_config|

      if machine['type'] == 'ubuntu'
        box_name = 'ubuntu-2204'
      else
        box_name = 'unknown'
      end

      box_postfix = system_architectury() == 'arm64' ? '-arm64' : ''

      hostname = "#{machine['name']}.localdev"


      machine_config.vm.box = "rethinc-oss/#{box_name}#{box_postfix}"
      machine_config.vm.hostname = hostname
      machine_config.vm.network "private_network", type: "dhcp"
      machine_config.hostmanager.aliases = ["www.#{hostname}"]

      machine_config.vm.provider "vmware_desktop" do |v|
        v.vmx["memsize"] = machine['memory']
        v.vmx["numvcpus"] = machine['cpus']
        v.gui = true
      end

      machine_config.vm.provider "virtualbox" do |v|
        v.memory = machine['memory']
        v.cpus = machine['cpus']
        v.gui = true
      end

      machine_config.vm.provision "shell" do |s|
        s.path = ".devsupport/scripts/install_puppet_agent.sh"
        s.keep_color = true
        s.env = {"PUPPET_VERSION" => "7"}
      end

      machine_config.vm.provision :puppet do |puppet|
        puppet.environment_path  = ".devsupport/puppet/"
        puppet.environment       = "localdev"
        puppet.hiera_config_path = ".devsupport/puppet/hiera-global.yaml"
     #   puppet.options           = ['--verbose']
      end

      machine_config.vm.synced_folder ".", "/vagrant"
    end
  end
end
