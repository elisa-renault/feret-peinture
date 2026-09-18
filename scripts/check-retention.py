"""Exercise backup cleanup against disposable fixtures, never real backups."""
import importlib.util
from pathlib import Path
import tempfile
import time

spec = importlib.util.spec_from_file_location('retention', Path(__file__).with_name('retention-deployment-backups.py'))
module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(module)
with tempfile.TemporaryDirectory() as folder:
    base = Path(folder)
    root = base / 'backups'
    root.mkdir()
    nested = root / 'nested'
    nested.mkdir()
    old = nested / 'backup.sql'
    old.write_text('fixture')
    outside = base / 'outside.sql'
    outside.write_text('must remain')
    (root / 'external-link').symlink_to(outside)
    now = time.time()
    assert module.cleanup(root, now, True) == 0
    assert module.cleanup(root, now + 31 * 86400, False) == 1
    assert old.exists()
    assert module.cleanup(root, now + 31 * 86400, True) == 1
    assert not old.exists() and outside.exists()
print('PASS: fresh files retained, dry-run safe, expired fixture removed, external symlink ignored.')
