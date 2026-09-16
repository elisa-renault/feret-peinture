#!/bin/sh
set -eu
cd "$(dirname "$0")/.."
find wp-content scripts tests -type f -name '*.php' -exec sh -c 'for file do php -l "$file" || exit 1; done' sh {} +
