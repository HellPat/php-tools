{
    # Pinning packages with URLs inside a Nix expression
    # https://nix.dev/tutorials/first-steps/towards-reproducibility-pinning-nixpkgs#pinning-packages-with-urls-inside-a-nix-expression
    # Picking the commit can be done via https://status.nixos.org,
    # which lists all the releases and the latest commit that has passed all tests.
    pkgs ? import (fetchTarball "https://github.com/NixOS/nixpkgs/archive/080166c15633801df010977d9d7474b4a6c549d7.tar.gz") {},

    php ? pkgs.php83.buildEnv {
          extensions = ({ enabled, all }: enabled ++ (with all; [
              redis
              openssl
              pcntl
              pdo_pgsql
              mbstring
              intl
              curl
              bcmath
              apcu
              xdebug
          ]));
          extraConfig = ''
            xdebug.mode=off
            xdebug.start_with_request=yes
            memory_limit=256M
          '';
        },

}:

pkgs.mkShell {
    buildInputs = [
        php
        pkgs.php83Packages.composer
        pkgs.process-compose
        pkgs.symfony-cli
    ];

    shellHook = ''
        export COMPOSER_MEMORY_LIMIT=-1
        php -v
    '';
}