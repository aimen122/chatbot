# Quick Start Guide - Deploy ColPR Chatbot

## 🚀 Fast Deployment Steps

### Step 1: Prepare Your Code (5 minutes)

1. **Update file paths** ✅ (Already done)
2. **Create GitHub repository:**
   ```bash
   git init
   git add .
   git commit -m "Ready for deployment"
   git branch -M main
   git remote add origin https://github.com/YOUR_USERNAME/colpr-chatbot.git
   git push -u origin main
   ```

### Step 2: Deploy to Render.com (10 minutes)

1. Go to https://render.com
2. Sign up (free account)
3. Click "New +" → "Web Service"
4. Connect your GitHub repository
5. Configure:
   - **Name**: `colpr-chatbot`
   - **Environment**: `Python 3`
   - **Build Command**: `pip install -r requirements.txt`
   - **Start Command**: `gunicorn test:app --bind 0.0.0.0:$PORT --workers 2 --timeout 120`
6. Add Environment Variables:
   - `GEMINI_API_KEY`: Your API key
   - `DB_HOST`: Your database host (if using external DB)
   - `DB_USER`: Database username
   - `DB_PASSWORD`: Database password
   - `DB_NAME`: Database name
7. Click "Create Web Service"
8. Wait 5-10 minutes for deployment
9. Copy your URL: `https://colpr-chatbot.onrender.com`

### Step 3: Add to Your Website (5 minutes)

**Option A: Floating Widget (Recommended)**

Copy this code and paste it before `</body>` tag on your website:

```html
<!-- ColPR Chatbot Widget -->
<div id="colpr-chatbot-widget" style="position: fixed; bottom: 20px; right: 20px; width: 400px; height: 600px; z-index: 10000; display: none; box-shadow: 0 4px 20px rgba(0,0,0,0.3); border-radius: 15px; overflow: hidden; background: white;">
    <button onclick="closeChatbot()" style="position: absolute; top: 10px; right: 10px; background: #ff4444; color: white; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; z-index: 10001;">×</button>
    <iframe src="YOUR_CHATBOT_URL_HERE" width="100%" height="100%" frameborder="0"></iframe>
</div>

<button id="chatbot-toggle" onclick="openChatbot()" style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; border-radius: 50%; background: #1a3c6e; color: white; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 10001; font-size: 28px;">💬</button>

<script>
function openChatbot() {
    document.getElementById('colpr-chatbot-widget').style.display = 'block';
    document.getElementById('chatbot-toggle').style.display = 'none';
}
function closeChatbot() {
    document.getElementById('colpr-chatbot-widget').style.display = 'none';
    document.getElementById('chatbot-toggle').style.display = 'block';
}
</script>
```

**Replace `YOUR_CHATBOT_URL_HERE` with your Render URL**

**Option B: Full Page**

Create a `/chat` page and add:
```html
<iframe src="YOUR_CHATBOT_URL_HERE" width="100%" height="100vh" frameborder="0"></iframe>
```

### Step 4: Test (2 minutes)

1. Visit your website
2. Click the chat button
3. Test the chatbot functionality
4. Verify all features work

## ✅ Done!

Your chatbot is now live and integrated with your website!

## 📝 Important Notes

1. **Free Tier Limitations:**
   - Render free tier spins down after 15 minutes of inactivity
   - First request after spin-down takes ~30 seconds
   - Consider paid tier for production

2. **Database:**
   - You can use Render's free PostgreSQL
   - Or keep using your existing MySQL database
   - Update `db.py` connection string

3. **Custom Domain:**
   - In Render dashboard → Settings → Custom Domain
   - Add your domain and update DNS

4. **Monitoring:**
   - Check Render logs for errors
   - Monitor Gemini API usage
   - Track database performance

## 🆘 Troubleshooting

**Chatbot not loading?**
- Check Render logs
- Verify environment variables
- Check if service is running

**CORS errors?**
- Already enabled in code
- Check browser console for specific errors

**Slow responses?**
- Upgrade to paid tier
- Optimize database queries
- Use caching

## 📞 Support

For issues, check:
- Render dashboard logs
- Browser console errors
- Database connection status

