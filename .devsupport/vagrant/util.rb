def system_architectury()
    if Vagrant::Util::Platform.windows? && RUBY_PLATFORM.start_with?("x64")
        "amd64"
    elsif Vagrant::Util::Platform.darwin?
        if `sysctl -i -n hw.optional.arm64`.to_i == 1
            "arm64"
        else
            "amd64"
        end
    else
        "unknown"
    end
end

# Source: https://stackoverflow.com/questions/19492738/demand-a-vagrant-plugin-within-the-vagrantfile/51925021#51925021
def require_plugins(plugins)
    if ARGV[0] != 'plugin'
      plugins_to_install = plugins.select { |plugin| not Vagrant.has_plugin? plugin }
      if not plugins_to_install.empty?
        puts "Installing plugins: #{plugins_to_install.join(' ')}"
        if system "vagrant plugin install #{plugins_to_install.join(' ')}"
          exec "vagrant #{ARGV.join(' ')}"
        else
          abort "Installation of one or more plugins has failed. Aborting."
        end
      end
    end
end

def require_admin_mode
  if !(`reg query HKU\\S-1-5-19 2>&1` =~ /ERROR|FEHLER/).nil?
    abort 'You must run vagrant from an elevated shell (run as administrator)'
  end
end

def read_ip_address(vm)
  command = "ip a | grep 'inet' | grep -v '127.0.0.1' | grep -v '::1/128' | cut -d: -f2 | awk '{ print $2 }' | cut -f1 -d\"/\""
  result  = ""

#    puts "Processing #{ vm.name } ... "

  begin
    # sudo is needed for ifconfig
    vm.communicate.sudo(command) do |type, data|
      result << data if type == :stdout
    end
#      puts "Processing #{ vm.name } ... success"
  rescue
    result = "# NOT-UP"
#      puts "Processing #{ vm.name } ... not running"
  end

  result = result.chomp.split("\n").select { |hash| hash != "" }

  vm_host_only_interfaces = []
  vm.provider.driver.read_network_interfaces().each_value do |iface|
    if iface[:type] == :hostonly then
      vm_host_only_interfaces.push(iface[:hostonly])
    end
  end

  vm_host_only_interfaces = vm.provider.driver.read_host_only_interfaces().select { |iface| vm_host_only_interfaces.include? iface[:name]  }

  ip = "# NO-IP"
  result.each do |vm_ip|
    vm_host_only_interfaces.each do |iface|
      if vm_ip[/[0-9]+.[0-9]+.[0-9]+/] = iface[:ip][/[0-9]+.[0-9]+.[0-9]+/] then 
        ip = vm_ip
      end
    end
  end

  ip
end
