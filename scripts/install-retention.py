"""Configure retention on the known VPS without deleting current backups."""
from pathlib import Path
import shutil
import subprocess
import time

base = Path('/srv/apps/feret-peinture')
if not (base / 'wp').is_file() or not (base / 'backups').is_dir():
    raise SystemExit('Unexpected host layout')
snapshot = base / 'backups' / ('retention-config-' + time.strftime('%Y%m%dT%H%M%S'))
snapshot.mkdir(mode=0o700)
targets = {
    '/etc/systemd/journald.conf.d/90-retention.conf': '[Journal]\nMaxRetentionSec=30day\nMaxFileSec=1day\n',
    '/etc/cron.d/feret-backup-retention': '17 3 * * * root /usr/bin/python3 /srv/apps/feret-peinture/code/scripts/retention-deployment-backups.py --apply\n',
}
for filename, content in targets.items():
    path = Path(filename)
    if path.is_symlink():
        raise SystemExit('Refusing symlink configuration')
    if path.exists():
        shutil.copy2(path, snapshot / path.name)
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(content)
    path.chmod(0o644)
subprocess.run(['systemctl', 'restart', 'systemd-journald'], check=True)
subprocess.run(['systemctl', 'is-active', 'systemd-journald'], check=True)
print('Retention configured: global system journal 30 days; project backup cleanup daily.')
