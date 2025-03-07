require 'r10k/module_loader/puppetfile'
require 'r10k/content_synchronizer'
require 'r10k/util/cleaner'

def fetch_puppet_dependencies(puppet_env_dir)
    Log4r::Logger.class_eval(%q{def debug1(data=nil, propagated=false) self.debug(data, propagated) end})
    Log4r::Logger.class_eval(%q{def debug2(data=nil, propagated=false) self.debug(data, propagated) end})
    @logger = Log4r::Logger.new("vagrant_r10k_logger")
    @logger.add(R10K::Logging.outputter)
    R10K::Logging.outputters.each do |output|
      @logger.add(output)
    end

    options = {
      basedir: puppet_env_dir,
      overrides: { force: false },
      moduledir:  puppet_env_dir + '/modules',
      puppetfile: puppet_env_dir + '/Puppetfile',
    }
    pool_size = 4

    loader = R10K::ModuleLoader::Puppetfile.new(**options)
    loaded_content = loader.load!
    modules   = loaded_content[:modules]
    R10K::ContentSynchronizer.concurrent_sync(modules, pool_size, @logger)

    R10K::Util::Cleaner.new(loaded_content[:managed_directories],
                                        loaded_content[:desired_contents],
                                        loaded_content[:purge_exclusions]).purge!
end
