class type::grav (
){
  include puppet_profiles::pebble

  $project = lookup('project')
  $domain_name = "${project['name']}.localdev"

  ::puppet_profiles::nginx::vhost::grav { $domain_name:
    domain_www          => false,
    domain_primary      => 'base',
    https               => true,
    user                => 'sysop',
    user_dir            => '/home/sysop',
    manage_user_dir     => false,
    website_dir         => '/vagrant',
    php_version         => $project['php_version'],
    php_modules         => $project['php_modules'],
    php_memory_limit    => $project['php_memory_limit'],
    php_upload_limit    => $project['php_upload_limit'],
    php_execution_limit => $project['php_execution_limit'],
  }
}
