class type::evocms (
){
  include puppet_profiles::pebble

  $project            = lookup('project')
  $project_name       = $project['name']
  $domain_name        = "${project_name}.localdev"
  $database_name      = regsubst($domain_name,'\\.','_', 'G')
  $app_key            = "base64:${base64('encode', fqdn_rand_string(32))}"
  $machine_defiition  = $project['machines'].filter |$machine| { $machine['name'] == $trusted['hostname'] }[0]
  $machine_roles      = $machine_defiition['roles'].map |$role| { "puppet_profiles::role::${role}" }
  $mailhog_enabled    = member($machine_roles, 'puppet_profiles::role::server::mailhog')

  ::puppet_profiles::nginx::vhost::evocms { $domain_name:
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

  mysql::db { $database_name:
    user     => $database_name,
    password => $database_name,
    host     => '%',
    grant    => ['ALL'],
  }
}
