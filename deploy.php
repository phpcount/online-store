<?php
namespace Deployer;

require 'recipe/symfony.php';

set('ssh_type', 'native');
set('ssh_multiplexing', false);
set('bin/console', '{{bin/php}} {{release_path}}/bin/console');

// Project name
set('application', 'online_store');

// Project repository
set('repository', 'git@github.com:phpcount/online-store.git');

// [Optional] Allocate tty for git clone. Default value is false.
// set('git_tty', true);

set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader --no-scripts');

// Quantity of releases
set('keep_releases', 2);

// Shared files/dirs between deploys
add('shared_files', ['.env.local']);
add('shared_dirs', ['var/log', 'public/uploads']);

// Writable dirs by web server
add('writable_dirs', ['var/log', 'var/cache', 'public/uploads']);
set('allow_anonymous_stats', false);

// Hosts

host('192.168.56.101')
    ->hostname('192.168.56.101')
    ->port(22)
    ->user('home')
    ->identityFile('~/.ssh/id_rsa')
    ->forwardAgent(true)
    // ->multiplexing(true)
    ->stage('production')
    ->set('branch', 'main')
    ->set('deploy_path', '/var/www/{{application}}');

// Tasks

task('pwd', function () {
    $result = run('pwd');
    writeln("Current dir: {$result}");
});

set('env', function () {
    return [
        'APP_ENV' => 'prod',
        'DATABASE_URL' => 'postgresql://rc_online_store:rc_online_store@192.168.56.101:5432/online_store?serverVersion=12&charset=utf8',
        'COMPOSER_MEMORY_LIMIT' => '512M'
    ];
});

task('deploy:build:assets', function () {
    cd('{{release_path}}');
    run('npm install');
    run('npm run build');

});

// for linux
// task('deploy:build_local_assets', function () {
//     upload('./public/bundles', '{{release_path}}/public/build/.');
//     upload('./public/bundles', '{{release_path}}/public/.');
// });

after('deploy:update_code', 'deploy:build:assets');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
// before('deploy:symlink', 'database:migrate');
before('deploy', 'success');
