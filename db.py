

# import mysql.connector

# # MySQL Configuration
# db_config = {
#     'host': 'localhost',
#     'user': 'root',  # Default XAMPP MySQL user
#     'password': '',   # Default XAMPP MySQL password (empty)
#     'database': 'glaxit_chatbot'
# }

# def get_db_connection():
#     """Establish a connection to the MySQL database."""
#     try:
#         conn = mysql.connector.connect(**db_config)
#         return conn
#     except mysql.connector.Error as err:
#         print(f"Database Connection Error: {err}")
#         return None

# def generate_user_id():
#     """Generate a unique numerical user ID by inserting a new user into the users table."""
#     conn = get_db_connection()
#     if conn:
#         try:
#             cursor = conn.cursor()
#             cursor.execute("INSERT INTO users (created_at) VALUES (NOW())")
#             conn.commit()
#             user_id = cursor.lastrowid
#             cursor.close()
#             conn.close()
#             return user_id
#         except mysql.connector.Error as err:
#             print(f"Error generating user ID: {err}")
#             return None
#     return None

# def log_chat_message(user_id, user_message, bot_response=None):
#     """Log user message and bot response to the chat_logs table with numerical user_id."""
#     conn = get_db_connection()
#     if conn:
#         try:
#             cursor = conn.cursor()
#             cursor.execute(
#                 "INSERT INTO chat_logs (user_id, user_message) VALUES (%s, %s)",
#                 (user_id, user_message)
#             )
#             conn.commit()
#             chat_id = cursor.lastrowid
#             if bot_response:
#                 cursor.execute(
#                     "UPDATE chat_logs SET bot_response = %s WHERE id = %s",
#                     (bot_response, chat_id)
#                 )
#                 conn.commit()
#             cursor.close()
#             conn.close()
#             return chat_id
#         except mysql.connector.Error as err:
#             print(f"Error logging chat message: {err}")
#             return None
#     return None

# def store_proposal(user_id, proposal_type, requirements, estimated_price, estimated_timeline, proposal_content, rejection_reason=None):
#     """Store a proposal in the proposals table with numerical user_id and full proposal content."""
#     conn = get_db_connection()
#     if conn:
#         try:
#             cursor = conn.cursor()
#             cursor.execute(
#                 "INSERT INTO proposals (user_id, proposal_type, requirements, estimated_price, estimated_timeline, proposal_content, rejection_reason) VALUES (%s, %s, %s, %s, %s, %s, %s)",
#                 (user_id, proposal_type, requirements, estimated_price, estimated_timeline, proposal_content, rejection_reason)
#             )
#             conn.commit()
#             cursor.close()
#             conn.close()
#             return True
#         except mysql.connector.Error as err:
#             print(f"Error storing proposal: {err}")
#             return False
#     return False

# def get_all_services_with_pricing():
#     """Get all services with their price ranges and timelines from the database."""
#     conn = get_db_connection()
#     if conn:
#         try:
#             cursor = conn.cursor(dictionary=True)
#             cursor.execute("""
#                 SELECT 
#                     category,
#                     service_name,
#                     description,
#                     min_price,
#                     max_price,
#                     min_timeline,
#                     max_timeline
#                 FROM services 
#                 ORDER BY category, service_name
#             """)
#             services = cursor.fetchall()
#             cursor.close()
#             conn.close()
#             return services
#         except mysql.connector.Error as err:
#             print(f"Error fetching services: {err}")
#             return []
#     return []

# def format_price_for_display(min_price, max_price):
#     """Format price range for display (convert to k notation if needed)."""
#     def format_single_price(price):
#         if price >= 1000:
#             return f"${price/1000:.0f}k" if price % 1000 == 0 else f"${price/1000:.1f}k"
#         return f"${price:.0f}"
    
#     return f"{format_single_price(min_price)}–{format_single_price(max_price)}"

# def format_timeline_for_display(min_timeline, max_timeline):
#     """Format timeline range for display in exact days with proper conversion."""
#     # Convert days to appropriate format (days/weeks/months)
#     if max_timeline <= 14:  # Up to 2 weeks - show in days
#         return f"{min_timeline}–{max_timeline} days"
#     elif max_timeline <= 60:  # Up to 2 months - show in weeks
#         min_weeks = round(min_timeline / 7, 1)
#         max_weeks = round(max_timeline / 7, 1)
#         # Remove .0 if it's a whole number
#         min_weeks_str = f"{min_weeks:.0f}" if min_weeks.is_integer() else f"{min_weeks:.1f}"
#         max_weeks_str = f"{max_weeks:.0f}" if max_weeks.is_integer() else f"{max_weeks:.1f}"
#         return f"{min_weeks_str}–{max_weeks_str} weeks"
#     else:  # More than 2 months - show in months
#         min_months = round(min_timeline / 30, 1)
#         max_months = round(max_timeline / 30, 1)
#         # Remove .0 if it's a whole number
#         min_months_str = f"{min_months:.0f}" if min_months.is_integer() else f"{min_months:.1f}"
#         max_months_str = f"{max_months:.0f}" if max_months.is_integer() else f"{max_months:.1f}"
#         return f"{min_months_str}–{max_months_str} months"
    
# def get_all_service_names():
#     """Get all service names from the database for validation."""
#     conn = get_db_connection()
#     if conn:
#         try:
#             cursor = conn.cursor(dictionary=True)
#             cursor.execute("SELECT service_name FROM services")
#             services = cursor.fetchall()
#             cursor.close()
#             conn.close()
#             return [service['service_name'].lower() for service in services]
#         except mysql.connector.Error as err:
#             print(f"Error fetching service names: {err}")
#             return []
#     return []

# def get_service_pricing_reference():
#     """Get formatted service pricing reference from database."""
#     services = get_all_services_with_pricing()
    
#     if not services:
#         return "Service pricing information is currently unavailable."
    
#     # Group by category
#     categories = {}
#     for service in services:
#         category = service['category']
#         if category not in categories:
#             categories[category] = []
        
#         price_range = format_price_for_display(service['min_price'], service['max_price'])
#         timeline_range = format_timeline_for_display(service['min_timeline'], service['max_timeline'])
        
#         categories[category].append({
#             'service_name': service['service_name'],
#             'price_range': price_range,
#             'timeline_range': timeline_range,
#             'description': service['description']
#         })
    
#     # Build the reference string
#     reference = ""
#     for category, service_list in categories.items():
#         reference += f"{category}\n\n"
#         for service in service_list:
#             reference += f"{service['service_name']}: {service['price_range']}, {service['timeline_range']}\n"
#         reference += "\n"
    
#     return reference.strip()


import mysql.connector

# MySQL Configuration
db_config = {
    'host': 'localhost',
    'user': 'root',  # Default XAMPP MySQL user
    'password': '',   # Default XAMPP MySQL password (empty)
    'database': 'glaxit_chatbot'
}

def get_db_connection():
    """Establish a connection to the MySQL database."""
    try:
        conn = mysql.connector.connect(**db_config)
        return conn
    except mysql.connector.Error as err:
        print(f"Database Connection Error: {err}")
        return None

def create_user_with_details(name, email, contact, company=None):
    """Create a new user with all details in the users table."""
    conn = get_db_connection()
    if conn:
        try:
            cursor = conn.cursor()
            
            # Check if user already exists with this email
            cursor.execute("SELECT id FROM users WHERE email = %s", (email,))
            existing_user = cursor.fetchone()
            
            if existing_user:
                print(f"⚠️ User already exists with email: {email}")
                cursor.close()
                conn.close()
                return existing_user[0]  # Return existing user ID
            
            # Insert new user with all details
            cursor.execute(
                "INSERT INTO users (name, email, contact, company, created_at) VALUES (%s, %s, %s, %s, NOW())",
                (name, email, contact, company)
            )
            conn.commit()
            user_id = cursor.lastrowid
            cursor.close()
            conn.close()
            
            print(f"✅ New user created - ID: {user_id}, Name: {name}, Email: {email}")
            return user_id
            
        except mysql.connector.Error as err:
            print(f"❌ Error creating user with details: {err}")
            return None
    return None

def get_user_by_id(user_id):
    """Get user details by user ID."""
    conn = get_db_connection()
    if conn:
        try:
            cursor = conn.cursor(dictionary=True)
            cursor.execute(
                "SELECT id, name, email, contact, company, created_at FROM users WHERE id = %s",
                (user_id,)
            )
            user = cursor.fetchone()
            cursor.close()
            conn.close()
            return user
        except mysql.connector.Error as err:
            print(f"Error fetching user by ID: {err}")
            return None
    return None

def get_user_by_email(email):
    """Get user details by email."""
    conn = get_db_connection()
    if conn:
        try:
            cursor = conn.cursor(dictionary=True)
            cursor.execute(
                "SELECT id, name, email, contact, company, created_at FROM users WHERE email = %s",
                (email,)
            )
            user = cursor.fetchone()
            cursor.close()
            conn.close()
            return user
        except mysql.connector.Error as err:
            print(f"Error fetching user by email: {err}")
            return None
    return None

def log_chat_message(user_id, user_message, bot_response=None):
    """Log user message and bot response to the chat_logs table with numerical user_id."""
    conn = get_db_connection()
    if conn:
        try:
            cursor = conn.cursor()
            cursor.execute(
                "INSERT INTO chat_logs (user_id, user_message) VALUES (%s, %s)",
                (user_id, user_message)
            )
            conn.commit()
            chat_id = cursor.lastrowid
            if bot_response:
                cursor.execute(
                    "UPDATE chat_logs SET bot_response = %s WHERE id = %s",
                    (bot_response, chat_id)
                )
                conn.commit()
            cursor.close()
            conn.close()
            return chat_id
        except mysql.connector.Error as err:
            print(f"Error logging chat message: {err}")
            return None
    return None

def store_proposal(user_id, proposal_type, requirements, estimated_price, estimated_timeline, proposal_content, rejection_reason=None):
    """Store a proposal in the proposals table with numerical user_id and full proposal content."""
    conn = get_db_connection()
    if conn:
        try:
            cursor = conn.cursor()
            cursor.execute(
                "INSERT INTO proposals (user_id, proposal_type, requirements, estimated_price, estimated_timeline, proposal_content, rejection_reason) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                (user_id, proposal_type, requirements, estimated_price, estimated_timeline, proposal_content, rejection_reason)
            )
            conn.commit()
            cursor.close()
            conn.close()
            return True
        except mysql.connector.Error as err:
            print(f"Error storing proposal: {err}")
            return False
    return False

def get_all_services_with_pricing():
    """Get all services with their price ranges and timelines from the database."""
    conn = get_db_connection()
    if conn:
        try:
            cursor = conn.cursor(dictionary=True)
            cursor.execute("""
                SELECT 
                    category,
                    service_name,
                    description,
                    min_price,
                    max_price,
                    min_timeline,
                    max_timeline
                FROM services 
                ORDER BY category, service_name
            """)
            services = cursor.fetchall()
            cursor.close()
            conn.close()
            return services
        except mysql.connector.Error as err:
            print(f"Error fetching services: {err}")
            return []
    return []

def format_price_for_display(min_price, max_price):
    """Format price range for display (convert to k notation if needed)."""
    def format_single_price(price):
        if price >= 1000:
            return f"${price/1000:.0f}k" if price % 1000 == 0 else f"${price/1000:.1f}k"
        return f"${price:.0f}"
    
    return f"{format_single_price(min_price)}–{format_single_price(max_price)}"

def format_timeline_for_display(min_timeline, max_timeline):
    """Format timeline range for display in exact days with proper conversion."""
    # Convert days to appropriate format (days/weeks/months)
    if max_timeline <= 14:  # Up to 2 weeks - show in days
        return f"{min_timeline}–{max_timeline} days"
    elif max_timeline <= 60:  # Up to 2 months - show in weeks
        min_weeks = round(min_timeline / 7, 1)
        max_weeks = round(max_timeline / 7, 1)
        # Remove .0 if it's a whole number
        min_weeks_str = f"{min_weeks:.0f}" if min_weeks.is_integer() else f"{min_weeks:.1f}"
        max_weeks_str = f"{max_weeks:.0f}" if max_weeks.is_integer() else f"{max_weeks:.1f}"
        return f"{min_weeks_str}–{max_weeks_str} weeks"
    else:  # More than 2 months - show in months
        min_months = round(min_timeline / 30, 1)
        max_months = round(max_timeline / 30, 1)
        # Remove .0 if it's a whole number
        min_months_str = f"{min_months:.0f}" if min_months.is_integer() else f"{min_months:.1f}"
        max_months_str = f"{max_months:.0f}" if max_months.is_integer() else f"{max_months:.1f}"
        return f"{min_months_str}–{max_months_str} months"
    
def get_all_service_names():
    """Get all service names from the database for validation."""
    conn = get_db_connection()
    if conn:
        try:
            cursor = conn.cursor(dictionary=True)
            cursor.execute("SELECT service_name FROM services")
            services = cursor.fetchall()
            cursor.close()
            conn.close()
            return [service['service_name'].lower() for service in services]
        except mysql.connector.Error as err:
            print(f"Error fetching service names: {err}")
            return []
    return []

def get_service_pricing_reference():
    """Get formatted service pricing reference from database."""
    services = get_all_services_with_pricing()
    
    if not services:
        return "Service pricing information is currently unavailable."
    
    # Group by category
    categories = {}
    for service in services:
        category = service['category']
        if category not in categories:
            categories[category] = []
        
        price_range = format_price_for_display(service['min_price'], service['max_price'])
        timeline_range = format_timeline_for_display(service['min_timeline'], service['max_timeline'])
        
        categories[category].append({
            'service_name': service['service_name'],
            'price_range': price_range,
            'timeline_range': timeline_range,
            'description': service['description']
        })
    
    # Build the reference string
    reference = ""
    for category, service_list in categories.items():
        reference += f"{category}\n\n"
        for service in service_list:
            reference += f"{service['service_name']}: {service['price_range']}, {service['timeline_range']}\n"
        reference += "\n"
    
    return reference.strip()

def get_all_users():
    """Get all users from the database."""
    conn = get_db_connection()
    if conn:
        try:
            cursor = conn.cursor(dictionary=True)
            cursor.execute("""
                SELECT id, name, email, contact, company, created_at 
                FROM users 
                ORDER BY created_at DESC
            """)
            users = cursor.fetchall()
            cursor.close()
            conn.close()
            return users
        except mysql.connector.Error as err:
            print(f"Error fetching users: {err}")
            return []
    return []

def get_conversation_history(user_id, limit=50):
    """Get conversation history for a user from chat_logs table.
    Returns only complete conversation pairs (user message + bot response).
    Excludes the most recent message if it doesn't have a bot_response yet."""
    conn = get_db_connection()
    if conn:
        try:
            cursor = conn.cursor(dictionary=True)
            # Get conversation history, excluding messages without bot_response
            # This ensures we only include complete conversation pairs
            cursor.execute("""
                SELECT user_message, bot_response, created_at 
                FROM chat_logs 
                WHERE user_id = %s 
                AND bot_response IS NOT NULL
                AND bot_response != ''
                ORDER BY created_at ASC 
                LIMIT %s
            """, (user_id, limit))
            messages = cursor.fetchall()
            cursor.close()
            conn.close()
            return messages
        except mysql.connector.Error as err:
            print(f"Error fetching conversation history: {err}")
            return []
    return []