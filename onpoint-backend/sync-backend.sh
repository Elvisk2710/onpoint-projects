#!/usr/bin/env bash
# Copy the shared backend into each site folder. Run from onpoint-backend/:
#   ./sync-backend.sh            (all sites)
#   ./sync-backend.sh cleaning   (one site)
# Never touches a site's .env, .htaccess, uploads/ content, includes/layout.php or blog/ (each site's blog design is its own).
set -euo pipefail
cd "$(dirname "$0")"
ROOT=".."
sites=("${@:-cleaning security engineering}")
for s in ${sites[@]}; do
  dest="$ROOT/onpoint-$s"
  [ -d "$dest" ] || { echo "skip $s (no folder)"; continue; }
  mkdir -p "$dest/uploads"
  rsync -a --delete admin/ "$dest/admin/"
  rsync -a --delete api/ "$dest/api/"
  rsync -a --delete src/ "$dest/src/"
  cp schema.sql install.php .user.ini sitemap.php feed.php llms.php indexnow.php "$dest/"
  mkdir -p "$dest/migrations" && cp migrations/*.sql "$dest/migrations/"
  cp uploads/.htaccess "$dest/uploads/.htaccess"
  touch "$dest/uploads/.gitkeep"
  echo "synced -> $dest"
done
