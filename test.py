import requests
from sentence_transformers import SentenceTransformer
import faiss
import numpy as np
from flask import Flask, request, jsonify
from flask_cors import CORS
import os
import time
from datetime import datetime
from dotenv import load_dotenv
from db import create_user_with_details, get_user_by_email, log_chat_message, store_proposal, get_all_services_with_pricing, get_service_pricing_reference, format_price_for_display, format_timeline_for_display, get_conversation_history
# Load env vars
# Get the directory where this script is located
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
ENV_PATH = os.path.join(BASE_DIR, '.env')
load_dotenv(ENV_PATH)  # Explicitly load from the script's directory
API_KEY = os.getenv("GEMINI_API_KEY") or os.getenv("API_KEY")
if not API_KEY:
    raise ValueError(f"GEMINI_API_KEY or API_KEY not set in .env file! (Looking in: {ENV_PATH})")

# Gemini API configuration
MODEL_ID = None  # Will be auto-detected on first use
AVAILABLE_MODEL = None  # Cached available model name
# API URL will be constructed in the function with the API key


EMBED_MODEL_NAME = "all-MiniLM-L6-v2"  # Fast, lightweight embedding model for semantic search
CHUNK_SIZE = 800
TOP_K = 4
TXT_FILE_PATH = os.path.join(BASE_DIR, "colprsc.txt")
THRESHOLD_VALUE = 50000  # Threshold value

# Initialize Flask app
app = Flask(__name__)
CORS(app)  # Enable CORS for website integration

# Global variables for AI components
chunks = None
index = None
embed_model = None
deal_state = {
    "user_id": None,
    "requirements": [],
    "matched_services": [],  # Store matched service names
    "status": "collecting",
    "estimated_price": None,
    "estimated_timeline": None
}

# User session management
user_sessions = {}

# Scripted answers (unchanged)
SCRIPTED_ANSWERS = {
    "What services do you offer?": """🚀 **ColPR Software Consultants Services Overview:**

**Custom Software Development**
- Enterprise Grade Applications
- Workflow Automation
- System Integration
- Legacy Modernization

**Mobile App Development**
- iOS & Android Apps
- React Native
- Flutter
- Mobile UI/UX Design

**AI Solutions**
- Custom AI Models
- Natural Language Processing
- Computer Vision
- Predictive Analytics

**Data & Analytics**
- Data Warehousing
- Business Intelligence
- Machine Learning
- Real-time Analytics
- OCR Solutions
- RPA Automation

**Cloud Solutions**
- AWS & Azure
- Cloud Migration
- DevOps & CI/CD
- Microservices Architecture

**Digital Transformation**
- Process Automation
- Digital Strategy
- Change Management
- Technology Consulting
- Implementation Services
- Project Management

**Which specific service are you interested in? I can provide more details!**""",

    "Tell me about ColPR Software Consultants": """🏢 **About ColPR Software Consultants - Premium Software Solutions**

**Who We Are:**
ColPR Software Consultants is a leading software consultancy delivering premium custom software solutions, web development, mobile applications, and digital transformation services for businesses worldwide.

**Our Expertise:**
- Extensive experience in software consultancy
- Proven track record of successful projects
- Global reach with personalized service
- Expert team leveraging cutting-edge technologies

**Our Mission:**
Our mission is to design custom software that learns, adapts, and lasts, built through innovation, automation, and a relentless focus on your success.

**Why Choose ColPR?**
- End-to-end solutions from concept to deployment
- Agile development with best practices
- Competitive pricing and personalized service
- Ongoing support & maintenance

**Ready to discuss how we can transform your business?**""",

    "How much does it cost?": """💰 **Customized Pricing for Your Needs**

At ColPR Software Consultants, we provide **tailored pricing** based on your unique project requirements. We avoid one-size-fits-all packages to ensure the best value for your business.

**To provide an accurate quote, I need to know:**
🔹 Which service are you interested in?
🔹 What are your project requirements?
🔹 What's your expected timeline?

**Please tell me: What specific service or solution are you looking for?**""",

    "Contact information": """📞 **Contact ColPR Software Consultants**

**Get in Touch:**
- 📧 Email: hello@colpr-software.com
- 🌐 Website: www.colpr-software.com
- 📍 Address: Not publicly listed (please contact us for details)

**Business Hours:**
- Monday-Friday: 9:00 AM - 6:00 PM
- Saturday: By appointment only
- Sunday: Closed

**Follow Us:**
- LinkedIn: ColPR Software Consultants
- Twitter: @ColPR_Software
- Facebook: ColPR Software Solutions

**Would you like to schedule a free initial consultation? I can help arrange it!**""",


    "Start project discussion": """🎯 **Project Discussion Started**

Great! Let's gather your project requirements step by step.

**Please tell me about your project:**
- What type of solution are you looking for?
- What are your main goals?
- Do you have any specific features in mind?
- What's your timeline and budget considerations?

**What would you like to start with?**"""
}


# -------- AI FUNCTIONS --------
def load_and_chunk(txt_path, chunk_size=CHUNK_SIZE):
    """Load text file and split into chunks"""
    try:
        with open(txt_path, "r", encoding="utf-8") as f:
            text = f.read()
    except FileNotFoundError:
        print(f"Warning: File {txt_path} not found. Using default text.")
        text = """
        ColPR Software Consultants is a leading software consultancy delivering premium custom software solutions, 
         mobile app development, digital transformation services, and IT consulting. 
        We specialize in creating tailored solutions that drive innovation and help businesses achieve their digital goals through cutting-edge technology.
        """
    
    chunks = []
    for i in range(0, len(text), chunk_size):
        chunk = text[i:i+chunk_size].strip()
        if chunk:
            chunks.append(chunk)
    return chunks

def build_faiss_index(chunks, model_name=EMBED_MODEL_NAME):
    """Build FAISS index from text chunks"""
    model = SentenceTransformer(model_name)
    if not chunks:
        chunks = ["ColPR Software Consultants provides IT services and software development."]
    
    embeddings = model.encode(chunks, show_progress_bar=True, convert_to_numpy=True)
    d = embeddings.shape[1]
    index = faiss.IndexFlatL2(d)
    index.add(embeddings.astype(np.float32))
    return index, model

def retrieve_top_k(query, index, chunks, embed_model, k=TOP_K):
    """Retrieve top k relevant chunks for a query"""
    q_emb = embed_model.encode([query], convert_to_numpy=True)
    D, I = index.search(q_emb.astype(np.float32), k)
    
    valid_indices = [idx for idx in I[0] if idx < len(chunks)]
    return [chunks[idx] for idx in valid_indices]

def debug_requirements_matching(requirements, matched_services, unmatched_requirements):
    """Debug function to see what's happening with requirement matching"""
    print("\n" + "="*50)
    print("DEBUG: REQUIREMENTS MATCHING ANALYSIS")
    print("="*50)
    print(f"User Requirements: {requirements}")
    print(f"Matched Services Count: {len(matched_services)}")
    print(f"Unmatched Requirements: {unmatched_requirements}")
    
    if matched_services:
        print("Matched Services Details:")
        for service in matched_services:
            print(f"  - {service['service_name']} (Category: {service['category']})")
            print(f"    Price: ${service['min_price']}-${service['max_price']}")
            print(f"    Timeline: {service['min_timeline']}-{service['max_timeline']} days")
    else:
        print("NO SERVICES MATCHED!")
        print("Available services from database:")
        all_services = get_all_services_with_pricing()
        for service in all_services:
            print(f"  - {service['service_name']} (Category: {service['category']})")
    print("="*50 + "\n")

def match_requirements_to_services(requirements):
    """Match user requirements to services using ENHANCED AI semantic matching."""
    global embed_model
    
    try:
        services = get_all_services_with_pricing()
        
        if not services:
            print("ERROR: No services found in database!")
            return [], requirements

        # Create comprehensive service representations for better matching
        service_data = []
        for service in services:
            # Create rich context for each service
            service_context = f"""
            Service: {service['service_name']}
            Category: {service['category']}
            Description: {service.get('description', '')}
            Price Range: ${service['min_price']} to ${service['max_price']}
            Timeline: {service['min_timeline']} to {service['max_timeline']} days
            """
            service_data.append({
                'service': service,
                'context': service_context.lower().strip()
            })

        # Combine user requirements
        user_context = " ".join(requirements).lower()
        print(f"🧠 USER REQUIREMENTS: {user_context}")
        print(f"🔍 AVAILABLE SERVICES: {len(services)} services")

        # Encode user requirements
        try:
            user_embedding = embed_model.encode([user_context], convert_to_numpy=True)
        except Exception as e:
            print(f"❌ FAILED TO ENCODE USER REQUIREMENTS: {e}")
            return [], requirements

        # Find best match using semantic similarity
        best_match = None
        best_similarity = 0
        similarity_scores = []

        for service_info in service_data:
            service = service_info['service']
            service_context = service_info['context']
            
            try:
                # Encode service context
                service_embedding = embed_model.encode([service_context], convert_to_numpy=True)
                
                # Calculate cosine similarity
                similarity = np.dot(service_embedding, user_embedding.T).flatten()[0]
                
                similarity_scores.append((service, similarity))
                
                print(f"  📊 {service['service_name']:.<30} similarity: {similarity:.3f}")
                
                if similarity > best_similarity:
                    best_similarity = similarity
                    best_match = service
                    
            except Exception as e:
                print(f"  ⚠️  Failed to process {service['service_name']}: {e}")
                continue

        # Sort by similarity for debugging
        similarity_scores.sort(key=lambda x: x[1], reverse=True)
        print("\n🏆 TOP 3 MATCHES:")
        for service, score in similarity_scores[:3]:
            print(f"   {service['service_name']}: {score:.3f}")

        # Return best match if similarity is reasonable
        matched_services = []
        if best_match and best_similarity >= 0.25:  # Lower threshold for AI matching
            matched_services = [best_match]
            print(f"✅ FINAL SELECTION: {best_match['service_name']} (score: {best_similarity:.3f})")
        else:
            print(f"❌ NO CONFIDENT MATCH (best score: {best_similarity:.3f})")

        return matched_services, requirements if not matched_services else []
        
    except Exception as e:
        print(f"💥 CRITICAL ERROR in match_requirements_to_services: {e}")
        return [], requirements

def calculate_estimate(matched_services):
    """Calculate estimate for the AI-matched service."""
    if not matched_services:
        # Provide helpful guidance when no match is found
        all_services = get_all_services_with_pricing()
        service_categories = set(service['category'] for service in all_services)
        
        guidance = f"""**I couldn't find an exact match for your requirements.**

Based on your description, here are our main service categories:

{chr(10).join([f"• **{category}**" for category in service_categories])}

**Please try being more specific, such as:**
- "I need a mobile app for iOS and Android"
- "I want to build a web application with user authentication"  
- "I need AI solutions for data analysis"
- "We require cloud migration for our existing system"

What specific type of solution are you looking for?"""
        
        return None, None, guidance

    # Use the AI-matched service
    service = matched_services[0]
    price_range = format_price_for_display(service['min_price'], service['max_price'])
    timeline_range = format_timeline_for_display(service['min_timeline'], service['max_timeline'])

    estimate_message = f"""**Perfect Match Found!** 🤖

Based on my analysis of your requirements, this service best matches your needs:

🎯 **{service['service_name']}** ({service['category']})

💵 **Budget:** {price_range}
📅 **Timeline:** {timeline_range}

*This pricing is based on our standard rates for {service['service_name']}.*"""

    return price_range, timeline_range, estimate_message

def build_proposal_with_user_details(user_session, requirements, proposal_type, estimated_price, estimated_timeline, matched_services, rejection_reason=None):
    """Build a comprehensive proposal with user details and exact requirements."""
    
    # Format user details
    user_details = f"""
**CLIENT INFORMATION**
- **Name:** {user_session['name']}
- **Email:** {user_session['email']}
- **Contact:** {user_session['contact']}
- **Company:** {user_session.get('company', 'Not specified')}
- **Session ID:** {[k for k, v in user_sessions.items() if v['user_id'] == user_session['user_id']][0] if any(v['user_id'] == user_session['user_id'] for v in user_sessions.values()) else 'N/A'}
"""
    
    # Format exact requirements
    requirements_section = "**EXACT CLIENT REQUIREMENTS**\n\n"
    for i, req in enumerate(requirements, 1):
        requirements_section += f"{i}. {req}\n"
    
    # Format matched services
    services_section = "**RECOMMENDED SERVICES**\n\n"
    for service in matched_services:
        services_section += f"• **{service['service_name']}** ({service['category']})\n"
        services_section += f"  - Description: {service.get('description', 'N/A')}\n"
        services_section += f"  - Budget: {format_price_for_display(service['min_price'], service['max_price'])}\n"
        services_section += f"  - Timeline: {format_timeline_for_display(service['min_timeline'], service['max_timeline'])}\n\n"
    
    # Build proposal based on type
    if proposal_type == 'ACCEPTED':
        proposal_content = f"""
# ✅ ACCEPTED PROPOSAL

## Project Overview
This proposal has been **ACCEPTED** by the client. The project details are confirmed and ready for execution.

{user_details}

## Requirements Analysis
{requirements_section}

## Service Recommendation
{services_section}

## Financial Summary
- **Total Budget:** {estimated_price}
- **Project Timeline:** {estimated_timeline}
- **Agreement Status:** ✅ ACCEPTED
- **Acceptance Date:** {np.datetime64('now')}

## Next Steps
1. Project team assignment within 24 hours
2. Initial project kickoff meeting
3. Detailed project planning
4. Development commencement

## Client Commitment
The client has reviewed and accepted this proposal. All requirements and pricing have been confirmed.

---
*This proposal was generated automatically by ColPR Software Consultants AI System*
"""
    
    elif proposal_type == 'REJECTED':
        reason_text = f"**Reason for Rejection:** {rejection_reason}" if rejection_reason else "**Reason for Rejection:** Budget concerns"
        
        proposal_content = f"""
# ❌ REJECTED PROPOSAL

## Proposal Status
This proposal has been **REJECTED** by the client. 

{user_details}

## Requirements Analysis
{requirements_section}

## Service Recommendation
{services_section}

## Financial Summary
- **Proposed Budget:** {estimated_price}
- **Project Timeline:** {estimated_timeline}
- **Agreement Status:** ❌ REJECTED
- **Rejection Date:** {np.datetime64('now')}

## Rejection Details
{reason_text}

## Follow-up Action
- Sales team to contact client within 24 hours
- Discuss alternative solutions or budget adjustments
- Explore flexible payment options if applicable

---
*This proposal was generated automatically by ColPR Software Consultants AI System*
"""
    
    elif proposal_type == 'ESCALATED':
        proposal_content = f"""
# 🚀 ESCALATED PROPOSAL - HIGH VALUE PROJECT

## Escalation Notice
This project has been **ESCALATED** to our specialized sales team due to high budget requirements.

{user_details}

## Requirements Analysis
{requirements_section}

## Service Recommendation
{services_section}

## Financial Summary
- **Estimated Budget:** {estimated_price}
- **Project Timeline:** {estimated_timeline}
- **Agreement Status:** 🚀 ESCALATED
- **Escalation Date:** {np.datetime64('now')}
- **Escalation Reason:** Project budget exceeds $50,000 threshold for automated processing

## Project Complexity Assessment
- **Budget Tier:** Enterprise Level
- **Service Complexity:** High
- **Customization Required:** Extensive
- **Team Requirement:** Specialized enterprise team

## Next Steps
1. **Priority contact** from enterprise sales specialist within 24 hours
2. **Customized solution** design session
3. **Detailed project scoping** with technical team
4. **Premium service package** presentation

## Enterprise Benefits
- Dedicated project manager
- Premium support package
- Customized solution architecture
- Priority development timeline

---
*This proposal was generated automatically by ColPR Software Consultants AI System and escalated for premium handling*
"""
    
    else:
        proposal_content = f"""
# 📋 PROPOSAL

{user_details}

## Requirements Analysis
{requirements_section}

## Service Recommendation
{services_section}

## Financial Summary
- **Estimated Budget:** {estimated_price}
- **Project Timeline:** {estimated_timeline}
- **Status:** {proposal_type}

---
*This proposal was generated automatically by ColPR Software Consultants AI System*
"""
    
    return proposal_content

def build_messages(retrieved_chunks, user_question, deal_state, requirements_list):
    """Build messages for the AI model with strict pricing instructions."""
    service_pricing_reference = get_service_pricing_reference()
    
    system = f"""
You are NOT Grok, ChatGPT,Gemini, or any other AI model. You are the ColPR Software Consultants company's official assistant.

Core tasks:
- Ask about project type and requirements first
- Provide help when user is unsure
- Use structured questions to gather requirements
- Match project with services from the database

REQUIREMENTS GATHERING STRATEGY:
- Start the conversation by asking the user to specify their project type using the main service categories as examples (e.g., Custom Software Development, Mobile App Development, AI Solutions).
- For every requirement provided by the user, **always** follow up with 5-6 highly relevant, structured, and specific clarifying questions (using examples) to ensure the requirement is complete and detailed before moving on.
- If the user provides a vague answer, ask structured questions focused on the 'function' or 'goal' of the solution, offering examples.

SERVICE AVAILABILITY RULE:
- If a user requests a service that is not available or not listed in the company’s official service offerings:
  → Politely respond: "I'm sorry, but we currently don't provide that specific service."
  → Then, mention the list of available services from the company's service database/file to guide the user (DO NOT show or mention budget or timeline)
  → Continue guiding the user to select or clarify a valid project requirement

SERVICE NOT SELECTED RULES:
If a user directly asks for complete project requirements without first selecting a service:
→ Respond: "I notice you haven't selected a service yet. Please choose a service from the available options below so I can help you with the specific requirements for that service."
→ List all available services from the company's service database
→ Guide the user to select one service before proceeding with requirement details

IRRELEVANT QUESTION HANDLING:
- If the customer asks irrelevant or general things (such as weather, politics, sports, random facts):
  → Politely say: "I'm here to assist you specifically with ColPR Software Consultants company. Could you please share your project or business requirements?"
  - Do NOT treat irrelevant questions as project requirements


CRITICAL REQUIREMENTS COLLECTION RULES:
1. ALWAYS collect complete requirements BEFORE providing any estimate
2. NEVER escalate or provide pricing until user explicitly says "complete requirement"
3. Requirements collection is complete ONLY when user says "complete requirement"
4. After every requirement provided by the user, ask: "Do you have any more requirements to add, or are you ready to complete the requirements? Please write 'complete requirement' if you're done."
5. If user asks for pricing or timeline before saying "complete requirement", respond with: "I'll be able to provide a detailed estimate once we've gathered all your project requirements. Please provide any additional requirements or say 'complete requirement' if you're done."

Requirements Completion Detection:
- If user says "complete requirement" - consider requirements complete
- THEN and ONLY THEN provide the budget estimate using EXACT pricing from the database: Budget: {deal_state.get('estimated_price', 'Not calculated')}, Timeline: {deal_state.get('estimated_timeline', 'Not calculated')}

CRITICAL RULE FOR PRICING:
- NEVER generate, calculate, or suggest any pricing or timelines yourself
- ALWAYS use the pricing and timeline provided by the system (from the database) in the format: Budget: {deal_state.get('estimated_price', 'Not calculated')}, Timeline: {deal_state.get('estimated_timeline', 'Not calculated')}
- If the user asks about costs, pricing, or estimates before completion, respond with: "I'll be able to provide a detailed estimate once we've gathered all your project requirements. Let's continue collecting the details!"
- Do not reference or invent any pricing or timeline numbers under ANY circumstances

THRESHOLD & ESCALATION RULES:
- Threshold value = $50,000
- ONLY AFTER providing estimate, check if Budget > Threshold value then -> esclate proposal
- Generate professional proposal with heading "ESCALATED PROPOSAL" with reason "threshold exceed concerns" in database
- Show message: "Thank you for showing your interest! Sales team will contact you within 24 hours. Goodbye!"
- END the conversation properly

If the lower bound of the estimated budget exceeds $50,000, immediately escalate to the Sales Team with the message: "Thank you for showing your interest! Sales team will contact you within 24 hours. Goodbye!" Do NOT ask for agreement in this case.

Else Ask customer: "Do you agree/disagree with this estimate?"

NEW AGREEMENT RULES:
If customer AGREES:
- Generate professional proposal with heading "ACCEPTED PROPOSAL"
- Include gathered requirements and use the EXACT locked estimate: Budget: {deal_state.get('estimated_price', 'Not specified')}, Timeline: {deal_state.get('estimated_timeline', 'Not specified')}
- Store as ACCEPTED proposal in database
- Show message: "Thank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We'll contact you within 24 hours to finalize details. Goodbye!"
- END the conversation properly

If customer DISAGREES:
- Ask: "Could you tell me the reason? Is it: 1) Budget issue, or 2) You want to update requirements?"
If BUDGET ISSUE:
- Generate professional proposal with heading "REJECTED PROPOSAL"
- Include gathered requirements and use the EXACT locked estimate: Budget: {deal_state.get('estimated_price', 'Not specified')}, Timeline: {deal_state.get('estimated_timeline', 'Not specified')}
- Store as REJECTED proposal with reason "Budget concerns" in database
- Show message: "Our Sales Team will contact you within 24 hours to discuss flexible options. Goodbye!"
- END the conversation properly

If UPDATE REQUIREMENTS:
- Re-gather requirements, clear previous estimate, and ask for agreement again

CRITICAL PRICE LOCK RULE:
- Once the system provides the estimated budget and timeline, these values are LOCKED and CANNOT be changed
- You MUST NOT revise, adjust, or generate alternative pricing or timelines
- For proposals, you MUST use the EXACT stored estimate: Budget: {deal_state.get('estimated_price', 'Not specified')}, Timeline: {deal_state.get('estimated_timeline', 'Not specified')}
- If the customer disagrees, escalate to the Sales Team without modifying the estimate

AFTER EACH REQUIREMENT:
- Always ask: "Do you have any other requirements? If not, please type 'complete requirement' to proceed with the estimate."

Service Pricing Reference (FROM DATABASE):
{service_pricing_reference}

Current deal state: {deal_state}
Current requirements: {requirements_list}
    """
    context = "\n\n---\n\n".join(retrieved_chunks)
    if len(context) > 2500:
        context = context[:2500] + "\n\n[TRUNCATED]"

    messages = [
        {"role": "system", "content": system},
        {"role": "user", "content": f"Context:\n{context}\n\nCustomer: {user_question}"}
    ]
    return messages

def get_available_gemini_models():
    """Get list of available Gemini models for this API key"""
    global AVAILABLE_MODEL
    
    if AVAILABLE_MODEL:
        return AVAILABLE_MODEL
    
    # Try to list models using the ListModels API
    api_versions = ["v1beta", "v1"]
    for api_version in api_versions:
        try:
            list_url = f"https://generativelanguage.googleapis.com/{api_version}/models?key={API_KEY}"
            r = requests.get(list_url, timeout=30)
            if r.status_code == 200:
                data = r.json()
                if "models" in data:
                    # Prioritize stable models over preview models
                    stable_models = []
                    preview_models = []
                    
                    for model in data["models"]:
                        model_name = model.get("name", "")
                        # Extract model name (remove "models/" prefix if present)
                        if model_name.startswith("models/"):
                            model_name = model_name[7:]  # Remove "models/" prefix
                        supported_methods = model.get("supportedGenerationMethods", [])
                        if "generateContent" in supported_methods:
                            # Separate stable and preview models
                            if "preview" in model_name.lower() or "experimental" in model_name.lower():
                                preview_models.append((model_name, api_version))
                            else:
                                stable_models.append((model_name, api_version))
                    
                    # Prefer stable models first (avoid preview models that overload easily)
                    for model_name, api_ver in stable_models:
                        if "flash" in model_name.lower():
                            print(f"✓ Found stable Gemini model: {model_name} (API: {api_ver})")
                            AVAILABLE_MODEL = (model_name, api_ver)
                            return AVAILABLE_MODEL
                    
                    # If no stable flash, use any stable model
                    if stable_models:
                        model_name, api_ver = stable_models[0]
                        print(f"✓ Found stable Gemini model: {model_name} (API: {api_ver})")
                        AVAILABLE_MODEL = (model_name, api_ver)
                        return AVAILABLE_MODEL
                    
                    # Only use preview models as last resort
                    if preview_models:
                        model_name, api_ver = preview_models[0]
                        print(f"⚠ Using preview Gemini model (may have rate limits): {model_name} (API: {api_ver})")
                        AVAILABLE_MODEL = (model_name, api_ver)
                        return AVAILABLE_MODEL
        except Exception as e:
            print(f"Error listing models for {api_version}: {e}")
            continue
    
    # Fallback: try common model names (prioritize stable models and v1 API)
    print("⚠ Could not list models, trying common model names...")
    # Prioritize stable models (without preview/experimental in name)
    stable_models = [
        "gemini-1.5-flash",  # Most stable and fast
        "gemini-1.5-pro",   # Stable pro version
        "gemini-pro"         # Original stable model
    ]
    # Try preview models only if stable ones fail
    preview_models = [
        "gemini-2.5-flash",
        "gemini-2.5-flash-lite"
    ]
    # Prefer v1 API over v1beta for better stability
    api_versions = ["v1", "v1beta"]
    
    # Try stable models first
    for model_name in stable_models:
        for api_version in api_versions:
            test_url = f"https://generativelanguage.googleapis.com/{api_version}/models/{model_name}:generateContent?key={API_KEY}"
            try:
                # Test with minimal payload
                test_payload = {
                    "contents": [{"role": "user", "parts": [{"text": "test"}]}],
                    "generationConfig": {"maxOutputTokens": 1}
                }
                r = requests.post(test_url, json=test_payload, timeout=10)
                if r.status_code == 200:
                    print(f"✓ Found working stable Gemini model: {model_name} (API: {api_version})")
                    AVAILABLE_MODEL = (model_name, api_version)
                    return AVAILABLE_MODEL
            except:
                continue
    
    # Only try preview models if stable ones don't work
    for model_name in preview_models:
        for api_version in api_versions:
            test_url = f"https://generativelanguage.googleapis.com/{api_version}/models/{model_name}:generateContent?key={API_KEY}"
            try:
                test_payload = {
                    "contents": [{"role": "user", "parts": [{"text": "test"}]}],
                    "generationConfig": {"maxOutputTokens": 1}
                }
                r = requests.post(test_url, json=test_payload, timeout=10)
                if r.status_code == 200:
                    print(f"⚠ Found working preview Gemini model (may have rate limits): {model_name} (API: {api_version})")
                    AVAILABLE_MODEL = (model_name, api_version)
                    return AVAILABLE_MODEL
            except:
                continue
    
    return None

def call_deepseek_chat(messages):
    """
    Call the Gemini API for AI chat responses.
    
    NOTE: This function uses your PAID Gemini API (gemini-2.5-flash) for generating chat responses.
    This is DIFFERENT from the embedding model (all-MiniLM-L6-v2) used for FAISS indexing.
    
    - Embedding model: Runs locally, used for semantic search/FAISS
    - Gemini API: Runs on Google's servers, used for chat responses
    """
    global AVAILABLE_MODEL
    
    # Get available model (cached after first call)
    model_info = get_available_gemini_models()
    if not model_info:
        print("✗ No available Gemini models found. Please check your API key.")
        return "I apologize, but I'm currently experiencing technical difficulties. Please try again shortly."
    
    model_name, api_version = model_info
    
    # Convert messages format from OpenAI-style to Gemini format
    # Gemini uses "contents" array with "role" and "parts" structure
    # For Gemini API, system messages should be prepended to the first user message
    contents = []
    system_content = ""
    
    # Extract system message if present
    for msg in messages:
        if msg.get("role") == "system":
            system_content = msg.get("content", "")
            break
    
    # Process conversation messages (skip system message)
    for msg in messages:
        role = msg.get("role", "user")
        if role == "system":
            continue
        
        # Gemini uses "user" and "model" roles (assistant becomes "model")
        gemini_role = "user" if role == "user" else "model"
        content = msg.get("content", "")
        
        # If this is the first user message and we have system content, prepend it
        if gemini_role == "user" and system_content and len(contents) == 0:
            # Prepend system instructions to the first user message in conversation
            content = system_content + "\n\n---\n\n" + content
        
        contents.append({
            "role": gemini_role,
            "parts": [{"text": content}]
        })
    
    payload = {
        "contents": contents,
        "generationConfig": {
            "temperature": 0.3,  # Lower temperature for more consistent, focused responses
            "maxOutputTokens": 2500,  # Increased to handle longer responses with examples
            "topP": 0.95,
            "topK": 40
        }
    }
    
    headers = {
        "Content-Type": "application/json"
    }
    
    api_url = f"https://generativelanguage.googleapis.com/{api_version}/models/{model_name}:generateContent?key={API_KEY}"
    
    # Retry configuration for 503 errors - increased delays for overloaded models
    max_retries = 4  # Increased retries for better reliability
    retry_delays = [3, 6, 12, 20]  # Longer exponential backoff: 3s, 6s, 12s, 20s (total ~41s wait)
    
    for attempt in range(max_retries):
        try:
            r = requests.post(api_url, headers=headers, json=payload, timeout=60)
            
            # Check for 503 Service Unavailable (model overloaded)
            if r.status_code == 503:
                if attempt < max_retries - 1:
                    delay = retry_delays[attempt]
                    print(f"Gemini API 503 Error (Model overloaded). Retrying in {delay} seconds... (Attempt {attempt + 1}/{max_retries})")
                    time.sleep(delay)
                    continue
                else:
                    print(f"Gemini API 503 Error: Model overloaded after {max_retries} attempts")
                    try:
                        error_data = r.json()
                        print(f"Error details: {error_data}")
                    except:
                        pass
                    return "I'm experiencing high demand right now. Please wait a moment and try again, or I'll retry automatically."
            
            r.raise_for_status()
            data = r.json()
            
            # Gemini response structure: data["candidates"][0]["content"]["parts"][0]["text"]
            if "candidates" in data and len(data["candidates"]) > 0:
                candidate = data["candidates"][0]
                
                # Check if response was truncated due to MAX_TOKENS
                if candidate.get("finishReason") == "MAX_TOKENS":
                    print("Warning: Response truncated due to MAX_TOKENS limit. Consider increasing maxOutputTokens.")
                
                # Extract text from response
                if "content" in candidate:
                    content = candidate["content"]
                    if "parts" in content and len(content["parts"]) > 0:
                        # Get text from parts
                        text_parts = []
                        for part in content["parts"]:
                            if "text" in part:
                                text_parts.append(part["text"])
                        if text_parts:
                            return " ".join(text_parts)
                
                # If no text found but we have a candidate, return a helpful message
                if candidate.get("finishReason") == "MAX_TOKENS":
                    return "I understand your requirements. Could you please provide more specific details about your project so I can help you better?"
            
            # Fallback error handling
            print(f"Unexpected Gemini API response structure: {data}")
            return "I apologize, but I'm currently experiencing technical difficulties. Please try again shortly."
            
        except requests.exceptions.Timeout:
            if attempt < max_retries - 1:
                delay = retry_delays[attempt]
                print(f"Gemini API Timeout. Retrying in {delay} seconds... (Attempt {attempt + 1}/{max_retries})")
                time.sleep(delay)
                continue
            else:
                print(f"Gemini API Timeout after {max_retries} attempts")
                return "The request is taking longer than expected. Please try again in a moment."
                
        except requests.exceptions.RequestException as e:
            # For other errors, check if it's retryable
            if hasattr(e, 'response') and e.response is not None:
                status_code = e.response.status_code
                # Retry on 429 (Too Many Requests) or 500-502 errors
                if status_code in [429, 500, 502] and attempt < max_retries - 1:
                    delay = retry_delays[attempt]
                    print(f"Gemini API Error {status_code}. Retrying in {delay} seconds... (Attempt {attempt + 1}/{max_retries})")
                    time.sleep(delay)
                    continue
                else:
                    print(f"Gemini API Error: {e}")
                    try:
                        error_data = e.response.json()
                        print(f"Error details: {error_data}")
                    except:
                        print(f"Error response: {e.response.text}")
            else:
                print(f"Gemini API Error: {e}")
            
            # Only return error message after all retries exhausted
            if attempt == max_retries - 1:
                return "I apologize, but I'm currently experiencing technical difficulties. Please try again shortly."
    
    # This should not be reached, but just in case
    return "I apologize, but I'm currently experiencing technical difficulties. Please try again shortly."

# -------- FLASK ROUTES --------

@app.route('/')
def home():
    """Serve the main chat interface"""
    with open('templates/chat.html', 'r', encoding='utf-8') as f:
        return f.read()


@app.route('/start-chat', methods=['POST'])
def start_chat():
    """Start a new chat session with user authentication"""
    global deal_state, user_sessions
    
    try:
        data = request.get_json()
        name = data.get('name', '').strip()
        email = data.get('email', '').strip()
        contact = data.get('contact', '').strip()
        company = data.get('company', '').strip()
        
        # Validate required fields
        if not name or not email or not contact:
            return jsonify({'error': 'Name, email, and contact are required'}), 400
        
        # Validate email format
        import re
        email_pattern = r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
        if not re.match(email_pattern, email):
            return jsonify({'error': 'Please enter a valid email address'}), 400
        
        # Create user in database with all details
        user_id = create_user_with_details(name, email, contact, company)
        if not user_id:
            return jsonify({'error': 'Failed to create user account. Please try again.'}), 500
        
        # Generate session ID
        session_id = f"session_{user_id}_{int(np.random.randint(1000, 9999))}"
        
        # Store user information in session
        user_sessions[session_id] = {
            'user_id': user_id,
            'name': name,
            'email': email,
            'contact': contact,
            'company': company,
            'created_at': np.datetime64('now')
        }
        
        # Initialize deal state for this session
        deal_state = {
            "user_id": user_id,
            "requirements": [],
            "matched_services": [],
            "status": "collecting",
            "estimated_price": None,
            "estimated_timeline": None
        }
        
        print(f"✅ New chat session started: {session_id}")
        print(f"👤 User: {name} ({email}) - ID: {user_id}")
        
        return jsonify({
            'session_id': session_id,
            'status': 'success',
            'message': 'Chat session started successfully',
            'user_id': user_id
        })
        
    except Exception as e:
        print(f"❌ Error starting chat session: {e}")
        return jsonify({'error': 'Failed to start chat session'}), 500

def get_user_session(session_id):
    """Get user session by session ID"""
    return user_sessions.get(session_id)

@app.route('/chat', methods=['POST'])
def chat():
    """Handle chat messages from the frontend"""
    global deal_state
    
    try:
        data = request.get_json()
        user_message = data.get('message', '').strip()
        session_id = data.get('session_id')
        
        # Check if session is valid
        user_session = get_user_session(session_id)
        if not user_session:
            return jsonify({'error': 'Invalid session. Please refresh and start again.'}), 401
        
        if not user_message:
            return jsonify({'error': 'Empty message'}), 400
        
        # Log user message to database with user ID from session
        log_chat_message(user_session['user_id'], user_message)
        
        # If session is locked, reject new messages
        if deal_state["status"] in ["escalated", "completed", "threshold_exceeded"]:
            response_data = {
                'response': "This chat session has ended. Please start a new conversation or contact our Sales Team.",
                'input_locked': True
            }
            log_chat_message(user_session['user_id'], user_message, response_data['response'])
            return jsonify(response_data)
        
        # Handle scripted answers
        if user_message in SCRIPTED_ANSWERS:
            response_data = {
                'response': SCRIPTED_ANSWERS[user_message],
                'scripted': True,
                'input_locked': False
            }
            log_chat_message(user_session['user_id'], user_message, response_data['response'])
            return jsonify(response_data)
        
        # Handle agreement responses
        if user_message.lower().startswith(('yes, i agree', 'no, i need')):
            if 'yes, i agree' in user_message.lower():
                # Customer agreed - generate ACCEPTED proposal
                proposal_content = build_proposal_with_user_details(
                    user_session=user_session,
                    requirements=deal_state["requirements"],
                    proposal_type='ACCEPTED',
                    estimated_price=deal_state['estimated_price'],
                    estimated_timeline=deal_state['estimated_timeline'],
                    matched_services=deal_state["matched_services"]
                )
                
                # Store ACCEPTED proposal
                requirements_text = "\n".join(deal_state["requirements"])
                store_proposal(
                    user_id=user_session['user_id'],
                    proposal_type='ACCEPTED',
                    requirements=requirements_text,
                    estimated_price=deal_state['estimated_price'],
                    estimated_timeline=deal_state['estimated_timeline'],
                    proposal_content=proposal_content
                )
                
                accepted_summary = "\n\n✅ **ACCEPTED PROPOSAL SUMMARY**\n\nThank you for agreeing! Your proposal has been ACCEPTED and sent to our team. We'll contact you within 24 hours to finalize details. Goodbye!"
                response_data = {
                    'response': proposal_content + accepted_summary,
                    'agreement_complete': True,
                    'input_locked': True,
                    'proposal_status': 'ACCEPTED'
                }
                deal_state = {"user_id": user_session['user_id'], "requirements": [], "matched_services": [], "status": "completed", "estimated_price": None, "estimated_timeline": None}
                print(f"ACCEPTED proposal generated. Deal state: {deal_state}")
                log_chat_message(user_session['user_id'], user_message, response_data['response'])
                return jsonify(response_data)
                
            else:
                # Customer disagreed - ask for reason
                response_data = {
                    'response': "Could you tell me the reason? Is it: 1) Budget issue, or 2) You want to update requirements?",
                    'requires_reason': True,
                    'input_locked': False
                }
                deal_state["status"] = "awaiting_reason"
                print(f"Awaiting reason for disagreement. Deal state: {deal_state}")
                log_chat_message(user_session['user_id'], user_message, response_data['response'])
                return jsonify(response_data)
        
        # Handle reason responses for disagreement
        if deal_state["status"] == "awaiting_reason":
            if any(keyword in user_message.lower() for keyword in ["budget", "price", "cost", "expensive", "too much", "1", "budget issue"]):
                # Budget issue - generate REJECTED proposal
                proposal_content = build_proposal_with_user_details(
                    user_session=user_session,
                    requirements=deal_state["requirements"],
                    proposal_type='REJECTED',
                    estimated_price=deal_state['estimated_price'],
                    estimated_timeline=deal_state['estimated_timeline'],
                    matched_services=deal_state["matched_services"],
                    rejection_reason='Budget concerns'
                )
                
                # Store REJECTED proposal
                requirements_text = "\n".join(deal_state["requirements"])
                store_proposal(
                    user_id=user_session['user_id'],
                    proposal_type='REJECTED',
                    requirements=requirements_text,
                    estimated_price=deal_state['estimated_price'],
                    estimated_timeline=deal_state['estimated_timeline'],
                    proposal_content=proposal_content,
                    rejection_reason='Budget concerns'
                )
                
                rejected_summary = "\n\n❌ **REJECTED PROPOSAL SUMMARY**\n\nOur Sales Team will contact you within 24 hours to discuss flexible options. Goodbye!"
                response_data = {
                    'response': proposal_content + rejected_summary,
                    'agreement_complete': True,
                    'input_locked': True,
                    'proposal_status': 'REJECTED',
                    'rejection_reason': 'Budget concerns'
                }
                deal_state = {"user_id": user_session['user_id'], "requirements": [], "matched_services": [], "status": "completed", "estimated_price": None, "estimated_timeline": None}
                print(f"REJECTED proposal generated (Budget concerns). Deal state: {deal_state}")
                log_chat_message(user_session['user_id'], user_message, response_data['response'])
                return jsonify(response_data)
            
            elif any(keyword in user_message.lower() for keyword in ["update requirements", "requirements", "features", "scope", "2", "update", "changes"]):
                # Update requirements
                response_data = {
                    'response': "Thank you for clarifying. Please provide the updated or additional requirements for your project, and I'll revise the estimate accordingly.",
                    'requires_reason': False,
                    'continue_requirements': True,
                    'input_locked': False
                }
                deal_state["status"] = "collecting"
                deal_state["estimated_price"] = None
                deal_state["estimated_timeline"] = None
                deal_state["matched_services"] = []
                print(f"Collecting updated requirements. Deal state: {deal_state}")
                log_chat_message(user_session['user_id'], user_message, response_data['response'])
                return jsonify(response_data)
            
            else:
                response_data = {
                    'response': "I'm sorry, I didn't understand. Could you please clarify if the issue is: 1) Budget/Time line issue, or 2) You want to update requirements?",
                    'requires_reason': True,
                    'input_locked': False
                }
                print(f"Re-prompting for reason. Deal state: {deal_state}")
                log_chat_message(user_session['user_id'], user_message, response_data['response'])
                return jsonify(response_data)
        
        # Handle requirements completion - ONLY "complete requirement" triggers estimation
        if user_message.lower() == "complete requirement":
            if not deal_state["requirements"]:
                response_data = {
                    'response': "I need to gather some requirements first before I can provide an estimate. Please tell me about your project requirements.",
                    'input_locked': False
                }
                log_chat_message(user_session['user_id'], user_message, response_data['response'])
                return jsonify(response_data)
            
            print(f"DEBUG: Starting requirement matching with: {deal_state['requirements']}")
            
            try:
                # Match requirements to services
                matched_services, unmatched_requirements = match_requirements_to_services(deal_state["requirements"])
                deal_state["matched_services"] = matched_services
                
                # If no services matched, provide better guidance
                if not matched_services:
                    # Get all available service categories for better suggestions
                    all_services = get_all_services_with_pricing()
                    categories = set(service['category'] for service in all_services)
                    
                    suggestion_message = f"""I couldn't match your specific requirements to our services. 

Based on your input: **{', '.join(deal_state['requirements'])}**

Here are our main service categories that might fit your needs:
{chr(10).join([f"• {category}" for category in categories])}

**Please be more specific about which type of service you need, such as:**
- "I need a mobile app for my business"
- "I want to automate our workflow processes"  
- "I need AI solutions for data analysis"
- "We require cloud migration services"

Which specific area are you interested in?"""
                    
                    response_data = {
                        'response': suggestion_message,
                        'input_locked': False
                    }
                    log_chat_message(user_session['user_id'], user_message, response_data['response'])
                    return jsonify(response_data)
                
                # Calculate estimate from matched services
                price_range, timeline_range, estimate_message = calculate_estimate(matched_services)
                
                # Store estimate
                deal_state["estimated_price"] = price_range
                deal_state["estimated_timeline"] = timeline_range
                
                # Check threshold
                total_min_price = sum(service['min_price'] for service in matched_services)
                print(f"DEBUG: Total min price: ${total_min_price}, Threshold: ${THRESHOLD_VALUE}")
                
                if total_min_price >= THRESHOLD_VALUE:
                    # Generate ESCALATED proposal with user details
                    proposal_content = build_proposal_with_user_details(
                        user_session=user_session,
                        requirements=deal_state["requirements"],
                        proposal_type='ESCALATED',
                        estimated_price=deal_state['estimated_price'],
                        estimated_timeline=deal_state['estimated_timeline'],
                        matched_services=deal_state["matched_services"]
                    )
                    
                    # Store ESCALATED proposal
                    requirements_text = "\n".join(deal_state["requirements"])
                    store_proposal(
                        user_id=user_session['user_id'],
                        proposal_type='ESCALATED',
                        requirements=requirements_text,
                        estimated_price=deal_state['estimated_price'],
                        estimated_timeline=deal_state['estimated_timeline'],
                        proposal_content=proposal_content,
                        rejection_reason='Threshold exceed concerns'
                    )
                    
                    escalation_summary = "\n\n📞 **ESCALATION NOTICE**\n\nThank you for showing your interest! Our sales team will contact you within 24 hours to discuss your high-value project requirements. Goodbye!"
                    response_data = {
                        'response': proposal_content + escalation_summary,
                        'threshold_exceeded': True,
                        'input_locked': True,
                        'proposal_status': 'ESCALATED',
                        'rejection_reason': 'Threshold exceed concerns'
                    }
                    deal_state = {"user_id": user_session['user_id'], "requirements": [], "matched_services": [], "status": "threshold_exceeded", "estimated_price": None, "estimated_timeline": None}
                    print(f"ESCALATION proposal generated (Threshold exceeded >= ${THRESHOLD_VALUE}). Deal state: {deal_state}")
                    log_chat_message(user_session['user_id'], user_message, response_data['response'])
                    return jsonify(response_data)
                
                # If below threshold, ask for agreement
                response_data = {
                    'response': estimate_message + "\n\nDo you agree with this estimate?",
                    'requires_agreement': True
                }
                print(f"Estimate provided: {estimate_message}. Deal state: {deal_state}")
                log_chat_message(user_session['user_id'], user_message, response_data['response'])
                return jsonify(response_data)
                
            except Exception as e:
                print(f"ERROR during requirement matching: {e}")
                response_data = {
                    'response': "I apologize, but I encountered an error while processing your requirements. Please try again with more specific requirements or contact our sales team for immediate assistance.",
                    'input_locked': False
                }
                log_chat_message(user_session['user_id'], user_message, response_data['response'])
                return jsonify(response_data)
        
        # Handle cancellation
        if any(word in user_message.lower() for word in ["not interested", "no thanks", "later", "not now", "cancel"]):
            response_data = {
                'response': "Thank you for your time. Your request has been escalated to our Sales Team, who will contact you within 24 hours if you wish to discuss further.",
                'escalation': True,
                'input_locked': True
            }
            deal_state = {"user_id": user_session['user_id'], "requirements": [], "matched_services": [], "status": "escalated", "estimated_price": None, "estimated_timeline": None}
            print(f"Escalation triggered by user cancellation. Deal state: {deal_state}")
            log_chat_message(user_session['user_id'], user_message, response_data['response'])
            return jsonify(response_data)
        
        # Normal conversation flow - store requirements and ask if complete
        if len(user_message.split()) > 2 and user_message.lower() != "complete requirement":
            deal_state["requirements"].append(user_message)
            print(f"Stored requirement: {user_message}. Current requirements: {deal_state['requirements']}")
            
            # After storing requirement, ask if complete
            retrieved = retrieve_top_k(user_message, index, chunks, embed_model, k=TOP_K)
            messages = build_messages(retrieved, user_message, deal_state, deal_state["requirements"])
            response = call_deepseek_chat(messages)
            
            # Safeguard: Prevent unsolicited pricing
            import re
            if re.search(r'Budget: \$[\d,k]+–\$[\d,k]+|Timeline:', response):
                print("AI tried to provide pricing before completion. Overriding response.")
                response = "I'll be able to provide a detailed estimate once we've gathered all your project requirements. Let's continue collecting the details!"
            
            response_data = {
                'response': response,
                'input_locked': False
            }
            log_chat_message(user_session['user_id'], user_message, response_data['response'])
            return jsonify(response_data)
        else:
            # For short messages or other cases, use normal AI response
            retrieved = retrieve_top_k(user_message, index, chunks, embed_model, k=TOP_K)
            messages = build_messages(retrieved, user_message, deal_state, deal_state["requirements"])
            response = call_deepseek_chat(messages)
            
            # Safeguard: Prevent unsolicited pricing
            import re
            if re.search(r'Budget: \$[\d,k]+–\$[\d,k]+|Timeline:', response):
                print("AI tried to provide pricing before completion. Overriding response.")
                response = "I'll be able to provide a detailed estimate once we've gathered all your project requirements. Let's continue collecting the details!"
            
            response_data = {
                'response': response,
                'input_locked': False
            }
            log_chat_message(user_session['user_id'], user_message, response_data['response'])
            return jsonify(response_data)
        
    except Exception as e:
        print(f"Error in chat endpoint: {e}")
        response_data = {
            'response': 'I apologize, but I encountered an error. Please try again.',
            'error': True,
            'input_locked': False
        }
        if 'user_session' in locals():
            log_chat_message(user_session['user_id'], user_message, response_data['response'])
        return jsonify(response_data), 500

@app.route('/reset', methods=['POST'])
def reset_chat():
    """Reset the conversation state"""
    global deal_state
    data = request.get_json()
    session_id = data.get('session_id')
    
    user_session = get_user_session(session_id)
    if not user_session:
        return jsonify({'error': 'Invalid session'}), 401
    
    user_id = user_session['user_id']
    deal_state = {
        "user_id": user_id,
        "requirements": [],
        "matched_services": [],
        "status": "collecting",
        "estimated_price": None,
        "estimated_timeline": None
    }
    return jsonify({'status': 'Chat reset successfully'})

@app.route('/human-help', methods=['POST'])
def human_help():
    """Handle human help button press - escalate to database and lock chat"""
    global deal_state
    
    try:
        data = request.get_json()
        session_id = data.get('session_id')
        
        # Check if session is valid
        user_session = get_user_session(session_id)
        if not user_session:
            return jsonify({'error': 'Invalid session. Please refresh and start again.'}), 401
        
        # Build escalation proposal content
        escalation_proposal = f"""
# 🚀 ESCALATED REQUEST - HUMAN AGENT REQUEST

## Escalation Notice
This request has been **ESCALATED** to our sales team due to user request for human agent assistance via Human Help button.

## Client Information
- **Name:** {user_session['name']}
- **Email:** {user_session['email']}
- **Contact:** {user_session['contact']}
- **Company:** {user_session.get('company', 'Not specified')}
- **User ID:** {user_session['user_id']}

## Request Details
- **Request Type:** Human Agent Escalation
- **Escalation Method:** Human Help Button
- **Escalation Date:** {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
- **Escalation Reason:** User requested to speak with a human agent via button

## Next Steps
1. **Priority contact** from sales team within 24 hours
2. **Personalized assistance** from our experts
3. **Direct communication** via call or email

---
*This escalation was generated automatically by ColPR Software Consultants AI System*
"""
        
        # Store escalation in database
        requirements_text = "\n".join(deal_state.get("requirements", [])) if deal_state.get("requirements") else "Human agent request - no specific requirements collected"
        store_proposal(
            user_id=user_session['user_id'],
            proposal_type='ESCALATED',
            requirements=requirements_text,
            estimated_price=deal_state.get('estimated_price') or 'N/A',
            estimated_timeline=deal_state.get('estimated_timeline') or 'N/A',
            proposal_content=escalation_proposal,
            rejection_reason='Human agent request'
        )
        
        # Human help response message
        human_help_response = """🤖

👨‍💼 **Human Agent Request Received**

Thank you for your request. Your inquiry has been escalated to our team. A human agent will reach out to you shortly.

📞 **What to expect:**

- Contact within 24 hours via call or email

- Priority support for your inquiry

- Personalized assistance from our experts

Thank you for choosing ColPR Software Consultants. Goodbye!

🤖

A human sales agent will contact you shortly. Thank you for considering ColPR Software Consultants!

💡 **Chat is locked. Please contact our Sales Team or start a new conversation.**"""
        
        response_data = {
            'response': human_help_response,
            'escalation': True,
            'input_locked': True
        }
        deal_state = {"user_id": user_session['user_id'], "requirements": [], "matched_services": [], "status": "escalated", "estimated_price": None, "estimated_timeline": None}
        print(f"✅ Escalation triggered by Human Help button. Stored in database. Deal state: {deal_state}")
        log_chat_message(user_session['user_id'], "[Human Help Button Pressed]", response_data['response'])
        return jsonify(response_data)
        
    except Exception as e:
        print(f"Error in human-help endpoint: {e}")
        return jsonify({'error': 'Failed to process human help request'}), 500

@app.route('/quick-answers', methods=['GET'])
def get_quick_answers():
    """Get available quick answers"""
    return jsonify(SCRIPTED_ANSWERS)

def initialize_ai():
    """Initialize AI components"""
    global chunks, index, embed_model
    print("Loading & indexing ColPR Software Consultants data...")
    chunks = load_and_chunk(TXT_FILE_PATH)
    index, embed_model = build_faiss_index(chunks)
    print("AI system initialized successfully!")
    print("Available quick answers:", list(SCRIPTED_ANSWERS.keys()))

if __name__ == '__main__':
    initialize_ai()
    port = int(os.environ.get('PORT', 5000))
    debug_mode = os.environ.get('FLASK_DEBUG', 'False').lower() == 'true'
    print(f"Starting Flask server on http://0.0.0.0:{port}")
    app.run(debug=debug_mode, host='0.0.0.0', port=port)