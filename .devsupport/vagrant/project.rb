require 'psych'

class Project
  def initialize(project_definition)
    raise ArgumentError, 'Argument is not a string' unless project_definition.is_a?(String)

    # Parse supplied project definition from file or string
    begin
      if File.file?(project_definition)
        @project_yaml = Psych.load_file(project_definition)
      elsif project_definition.start_with?("---")
        @project_yaml = Psych.load(project_definition);
      end
    rescue Psych::SyntaxError => e
      raise ArgumentError, ['Failed to parse the file', e]
    end

    # Validate minimaly needed project definition
    unless @project_yaml.include?('project')
      raise ArgumentError, 'Malformed project definition: Missing top-level project hash'
    end
    unless @project_yaml['project'].include?('type')
      raise ArgumentError, 'Malformed project definition: Missing type'
    end

    # Validate type-specific project definitions
    @project_yaml = self.validator(@project_yaml['project']['type']).validate(@project_yaml)
  end

  def validator(type)
#    Dir[File.dirname(__FILE__) + '/validator/{[!abstract_*]}*.rb'].each do |file|
    Dir[File.dirname(__FILE__) + '/validator/*.rb'].each do |file|
      require_relative 'validator/' + File.basename(file, File.extname(file))
    end

    validator_for_type = type.split('_').map(&:capitalize).join + "ProjectValidator"
    clazz = Object.const_get(validator_for_type)
    clazz.new
  end

  def machines
    @project_yaml['project']['machines']
  end

  def write_project_hiera(file)
    File.open(file, 'w') do |file|
      file.syswrite(Psych.dump(@project_yaml))
    end    
  end
end
