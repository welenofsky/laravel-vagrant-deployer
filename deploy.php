<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'src');

// Project repository
set('repository', 'git@github.com:welenofsky/some-git-repo.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', ['.env']);
add('shared_dirs', ['storage']);

// Writable dirs by web server
add('writable_dirs', ['{{release_path}}']);
set('allow_anonymous_stats', false);

// Hosts
host('staging') // staging
    ->stage('staging')
    ->hostname('some-hostname-from-ssh-config')
    ->forwardAgent()
    ->set('branch', 'master')
    ->set('http_user', 'deployusername:www-data')
    ->set('writable_mode', 'chown')
    ->set('writable_use_sudo', false)
    ->set('deploy_path', '/home/deployusername/{{application}}');


// Tasks
desc('Setup local dev box');
task('box:build', function () {
//    // Create virtualenvironment if it doesn't exist
    runLocally('[ ! -d .venv ] && python3 -m venv .venv/ || true');

    runLocally('. .venv/bin/activate && pip install -r requirements.txt');
    runLocally('. .venv/bin/activate && vagrant up --provision', ['timeout' => 600]);
    runLocally('composer install');
    runLocally('[ ! -f .env ] && cp .env.example .env && php artisan key:generate || true');
    runLocally('php artisan migrate --seed');
    runLocally('npm i && npm run dev');
});

desc('Destroy the virtual machine');
task('box:destroy', function () {
    if (askConfirmation('Are you sure you want to destroy this virtual machine?')) {
        runLocally('vagrant destroy -f');
    }
});

desc('Composer install the dependencies');
task('composer_install', function () {
    if (has('deploy_path')) {
        cd('{{deploy_path}}');
    }

    run('composer install');
});

desc('Build the assets');
task('build_assets', function () {
    run('cd {{release_path}} && npm i && npm run prod');
});
after('deploy:vendors', 'build_assets');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');

before('deploy', 'prd_confirmation');

task('reload:php-fpm', function () {
    run('sudo $(which systemctl) restart php8.0-fpm.service');
});
after('deploy', 'reload:php-fpm');

desc('If this is not being ran on dev/staging/localhost then confirm that they want to deploy');
task('prd_confirmation', function () {
    // If not running on localhost or staging
    if (!in_array(get('stage') ?: 'production', ['localhost', 'staging'])) {
        if (!askConfirmation('You are not deploying locally or to staging. You are most likely deploying to production right now. Are you certain you want to do this?')) {
            exit;
        }
    }
});
