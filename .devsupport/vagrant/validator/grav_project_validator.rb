#---
#project:
#  type: 'grav'
#  name: 'acme-website'
#  services: ['mailhog']
#  php_version: '8.1'
#  php_modules: ['bcmath', 'json', 'mbstring', 'xml', 'mysql', 'tokenizer']
#  php_memory_limit: '32M'
#  php_upload_limit: '10M'
#  php_execution_limit: 30
class GravProjectValidator
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

    roles = ['server::web']
    if project_yaml['project'].include?('services')
      project_yaml['project']['services'].each do |service|
        roles.push('server::mailhog') if service.casecmp('mailhog') == 0
      end
    end

    project_yaml['project']['machines'] = [{
      "name" => project_yaml['project']['name'],
      "type" => 'ubuntu',
      "memory" => 2048,
      "cpus" => 2,
      "disk" => 20480,
      "roles" => ['server::web'],
    }]

    project_yaml
  end
end
