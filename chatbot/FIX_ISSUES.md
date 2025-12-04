# Chatbot Issues and Fixes

## Issue 1: ImportError with huggingface_hub

### Problem
```
ImportError: cannot import name 'cached_download' from 'huggingface_hub'
```

### Root Cause
- `sentence-transformers==2.2.2` uses deprecated `cached_download` function
- Newer versions of `huggingface_hub` removed `cached_download` (deprecated in v0.17.0, removed in v0.20.0+)

### Solution Applied
Updated `requirements.txt`:
- Changed `sentence-transformers==2.2.2` to `sentence-transformers>=2.3.0`
- Added explicit `huggingface_hub>=0.20.0` dependency

### How to Fix

1. **Activate your virtual environment** (if using one):
   ```bash
   source venv/bin/activate  # Linux/Mac
   # or
   venv\Scripts\activate  # Windows
   ```

2. **Uninstall old incompatible packages**:
   ```bash
   pip uninstall sentence-transformers huggingface_hub -y
   ```

3. **Install updated requirements**:
   ```bash
   pip install -r requirements.txt --upgrade
   ```

4. **Verify installation**:
   ```bash
   python -c "from sentence_transformers import SentenceTransformer; print('✓ Import successful')"
   ```

## Issue 2: Gunicorn Worker Errors

### Problem
Workers failing to start due to import errors.

### Solution
After fixing the dependency issue, restart gunicorn:
```bash
gunicorn test:app --bind 0.0.0.0:9211 --workers 2 --timeout 120
```

## Alternative Fix (If Update Doesn't Work)

If updating `sentence-transformers` causes other issues, you can pin `huggingface_hub` to an older version:

```txt
sentence-transformers==2.2.2
huggingface_hub<0.20.0
```

But it's recommended to update `sentence-transformers` instead for better compatibility and security.

## Testing

After fixing, test the import:
```python
python -c "from sentence_transformers import SentenceTransformer; print('Success!')"
```

If successful, you should see: `Success!`

## Additional Notes

- Make sure your Python version is compatible (Python 3.8+)
- If you're using a virtual environment, ensure it's activated
- Clear any cached Python bytecode: `find . -type d -name __pycache__ -exec rm -r {} +`

