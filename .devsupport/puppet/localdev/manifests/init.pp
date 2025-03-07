node default {
  $project            = lookup('project')
  $project_type       = $project['type'];
  $project_class      = "type::${regsubst($project_type, '/', '::', 'G')}"

  $machine_defiition  = $project['machines'].filter |$machine| { $machine['name'] == $trusted['hostname'] }[0]
  $machine_roles      = $machine_defiition['roles'].map |$role| { "puppet_profiles::role::${role}" }

  include $machine_roles + $project_class
}
