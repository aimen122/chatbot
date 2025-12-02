# Deployment Guide for ColPR Chatbot

## Step 1: Prepare Your Application for Deployment

### 1.1 Update File Paths
The current code has hardcoded Windows paths. Update `test.py` line 29:

**Change:**
```python
TXT_FILE_PATH = r"E:\final\colprsc.txt"
```

**To:**
```python
TXT_FILE_PATH = os.path.join(BASE_DIR, "colprsc.txt")
```

### 1.2 Update Flask App Configuration
At the end of `test.py`, change the app.run() to be production-ready:

**Change:**
```python
if __name__ == '__main__':
    initialize_ai()
    print("Starting Flask server on http://localhost:5000")
    app.run(debug=True, host='0.0.0.0', port=5000)
```

**To:**
```python
if __name__ == '__main__':
    initialize_ai()
    port = int(os.environ.get('PORT', 5000))
    app.run(debug=False, host='0.0.0.0', port=port)
```

## Step 2: Choose a Hosting Platform

### Option A: Render.com (Recommended - Free tier available)
### Option B: Railway.app
### Option C: Heroku (Paid)
### Option D: AWS/GCP/Azure (Enterprise)

## Step 3: Deploy to Render.com (Step-by-Step)

### 3.1 Create GitHub Repository
1. Go to https://github.com
2. Create a new repository
3. Push your code:
```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/yourusername/colpr-chatbot.git
git push -u origin main
```

### 3.2 Deploy on Render
1. Go to https://render.com
2. Sign up/Login
3. Click "New +" → "Web Service"
4. Connect your GitHub repository
5. Configure:
   - **Name**: colpr-chatbot
   - **Environment**: Python 3
   - **Build Command**: `pip install -r requirements.txt`
   - **Start Command**: `gunicorn test:app --bind 0.0.0.0:$PORT --workers 2 --timeout 120`
   - **Instance Type**: Free (or paid for better performance)

### 3.3 Set Environment Variables in Render
Go to Environment section and add:
- `GEMINI_API_KEY`: Your Gemini API key
- `PORT`: (Auto-set by Render)
- Database connection variables (if using external DB):
  - `DB_HOST`
  - `DB_USER`
  - `DB_PASSWORD`
  - `DB_NAME`

### 3.4 Deploy
Click "Create Web Service" and wait for deployment.

## Step 4: Database Setup

### Option A: Use Render PostgreSQL (Free tier)
1. In Render dashboard, create "PostgreSQL" database
2. Get connection string
3. Update `db.py` to use PostgreSQL instead of MySQL

### Option B: Use External MySQL Database
1. Use services like:
   - PlanetScale (Free tier)
   - AWS RDS
   - Google Cloud SQL
2. Update connection in `db.py`

## Step 5: Integrate with Your Website

### Method 1: Embed as iframe (Easiest)

Add this to your website HTML where you want the chatbot:

```html
<iframe 
    src="https://your-chatbot-url.onrender.com" 
    width="100%" 
    height="600px" 
    frameborder="0"
    style="border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
</iframe>
```

### Method 2: Embed as Widget (Better UX)

Create a floating chat widget:

**Add to your website's HTML (before </body>):**
```html
<!-- Chatbot Widget -->
<div id="colpr-chatbot-widget" style="position: fixed; bottom: 20px; right: 20px; width: 400px; height: 600px; z-index: 1000; display: none; box-shadow: 0 4px 20px rgba(0,0,0,0.3); border-radius: 15px; overflow: hidden;">
    <iframe 
        src="https://your-chatbot-url.onrender.com" 
        width="100%" 
        height="100%" 
        frameborder="0">
    </iframe>
</div>

<!-- Chat Button -->
<button 
    id="chatbot-toggle" 
    onclick="toggleChatbot()"
    style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; border-radius: 50%; background: #1a3c6e; color: white; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 1001; font-size: 24px;">
    💬
</button>

<script>
function toggleChatbot() {
    const widget = document.getElementById('colpr-chatbot-widget');
    const button = document.getElementById('chatbot-toggle');
    
    if (widget.style.display === 'none') {
        widget.style.display = 'block';
        button.style.display = 'none';
    } else {
        widget.style.display = 'none';
        button.style.display = 'block';
    }
}
</script>
```

### Method 3: Full Page Integration

Create a dedicated page on your website:
1. Create `/chat` page on your website
2. Redirect to: `https://your-chatbot-url.onrender.com`
3. Or embed using iframe with full width/height

## Step 6: Update CORS Settings (If needed)

If you're embedding from a different domain, add CORS to `test.py`:

```python
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # Allow all origins, or specify your domain
```

Add to `requirements.txt`:
```
flask-cors==4.0.0
```

## Step 7: Testing

1. Test the deployed chatbot URL directly
2. Test embedding on your website
3. Test all features:
   - User registration
   - Chat functionality
   - Requirement gathering
   - Proposal generation

## Step 8: Custom Domain (Optional)

1. In Render dashboard, go to Settings
2. Add custom domain
3. Update DNS records as instructed
4. Update iframe/widget URLs to use custom domain

## Troubleshooting

### Issue: Application crashes on startup
- Check logs in Render dashboard
- Verify all environment variables are set
- Check database connection

### Issue: Slow response times
- Upgrade to paid tier for better resources
- Optimize FAISS index loading
- Use caching for embeddings

### Issue: CORS errors
- Add flask-cors and configure properly
- Check browser console for specific errors

## Security Considerations

1. **Never commit `.env` file** - Already in .gitignore
2. **Use HTTPS** - Render provides SSL automatically
3. **Rate limiting** - Consider adding Flask-Limiter
4. **Input validation** - Already implemented
5. **Database security** - Use connection pooling and prepared statements

## Performance Optimization

1. **Pre-load embeddings** - Load FAISS index on startup
2. **Cache responses** - Cache common queries
3. **Database indexing** - Index frequently queried columns
4. **CDN** - Use CDN for static assets (if any)

## Monitoring

1. Use Render's built-in monitoring
2. Add logging for errors
3. Monitor API usage (Gemini API limits)
4. Track database performance

## Next Steps

1. Deploy to staging first
2. Test thoroughly
3. Deploy to production
4. Monitor and optimize

