#---
#project:
#  type: 'shopware5'
#  name: 'acme-website'
#  services: ['mailhog']
#  php_version: '8.1'
#  php_modules: []
#  php_memory_limit: '256M'
#  php_upload_limit: '10M'
#  php_execution_limit: 360
class Shopware5ProjectValidator
  def validate(project_yaml)
    unless project_yaml['project'].include?('name')
      raise ArgumentError, 'Malformed project definition: Missing ''name'' propertry'
    end

    unless project_yaml['project'].include?('php_version')
      raise ArgumentError, 'Malformed project definition: Missing ''php_version'' property'
    end

    if project_yaml['project'].include?('machines')
      raise ArgumentError, 'Malformed project definition: Machines are hardcoded for this project type'
    end

    roles = ['server::mysql', 'server::web']
    if project_yaml['project'].include?('services')
      project_yaml['project']['services'].each do |service|
        roles.push('server::mailhog') if service.casecmp('mailhog') == 0
      end
    end

    project_yaml['project']['machines'] = [{
      "name" => project_yaml['project']['name'],
      "type" => 'ubuntu',
      "memory" => 4096,
      "cpus" => 4,
      "disk" => 20480,
      "roles" => roles,
    }]

    project_yaml
  end
end
