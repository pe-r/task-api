#---
#project:
#  type: infrastructure
#  machines:
#    - type: ubuntu
#      name: webserver
#      memory: 1024
#      cpus: 1
#      disk: 10240
class InfrastructureProjectValidator
  def validate(project_yaml)
    unless project_yaml['project'].include?('machines') && project_yaml['project']['machines'].length > 0
      raise ArgumentError, 'Malformed project definition: Missing machine definitions'
    end
    
    project_yaml['project']['machines'].each do |machine|
      unless machine.include?('type')
        raise ArgumentError, 'Malformed project: Missing machine type definition'
      end
      unless machine.include?('name')
        raise ArgumentError, 'Malformed project: Missing machine name definition'
      end
      unless machine.include?('memory')
        raise ArgumentError, 'Malformed project: Missing machine memory definition'
      end
      unless machine.include?('cpus')
        raise ArgumentError, 'Malformed project: Missing machine cpus definition'
      end
      unless machine.include?('disk')
        raise ArgumentError, 'Malformed project: Missing machine disk definition'
      end
    end

    project_yaml
  end
end
