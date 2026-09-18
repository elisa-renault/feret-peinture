"""Daily cleanup of deployment backups only. Dry-run unless --apply is given."""
import argparse
import os
from pathlib import Path
import stat
import time

ROOT = Path('/srv/apps/feret-peinture/backups')

def cleanup(root, now, apply=False):
    if root.is_symlink() or root.resolve() != root.absolute():
        raise RuntimeError('Refusing an indirect backup root')
    cutoff = now - 30 * 86400
    count = 0
    for directory, dirs, files in os.walk(root, followlinks=False):
        dirs[:] = [name for name in dirs if not (Path(directory) / name).is_symlink()]
        for name in files:
            path = Path(directory) / name
            info = path.lstat()
            if not stat.S_ISREG(info.st_mode):
                continue
            if not path.resolve().is_relative_to(root.resolve()):
                raise RuntimeError('Path escaped backup root')
            # Copying an old source preserves mtime sometimes; ctime dates entry creation/change.
            if max(info.st_mtime, info.st_ctime) < cutoff:
                count += 1
                if apply:
                    path.unlink()
    return count

if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('--apply', action='store_true')
    args = parser.parse_args()
    if not ROOT.is_dir():
        raise SystemExit('Missing backup directory')
    count = cleanup(ROOT, time.time(), args.apply)
    print(f'Expired deployment backup files: {count}; applied: {args.apply}')
