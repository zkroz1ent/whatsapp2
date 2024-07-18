```mermaid
erDiagram
    users {
        id INT
        username VARCHAR
        role VARCHAR
        created_at TIMESTAMP
    }
    follows {
        following_user_id INT
        followed_user_id INT
        created_at TIMESTAMP
    }
    posts {
        id INT
        title VARCHAR
        body TEXT
        user_id INT
        status VARCHAR
        created_at TIMESTAMP
    }
    commission {
        id INT
        name VARCHAR
    }
    conversation {
        id INT
        user_id INT
        message_id INT
    }
    group_conversation {
        id INT
        name VARCHAR
    }
    group_conversation_users {
        user_id INT
        group_conversation_id INT
    }
    message {
        id INT
        sender_id INT
        commission_id INT
        content LONGTEXT
        is_global TINYINT
    }
    messenger_messages {
        id BIGINT
        body LONGTEXT
        headers LONGTEXT
        queue_name VARCHAR
        created_at DATETIME
        available_at DATETIME
        delivered_at DATETIME
    }
    notification {
        id INT
        message_id INT
        message_content LONGTEXT
        created_at DATETIME
    }
    notification_user {
        notification_id INT
        user_id INT
    }
    post {
        id INT
        author_id INT
        commission_id INT
        content LONGTEXT
        created_at DATETIME
    }
    user_commission {
        user_id INT
        commission_id INT
    }

    users ||--|| follows : follows
    users ||--|| posts : author
    users ||--|{ conversation : initiates
    users ||--o{ group_conversation_users : member
    users ||--|| notification_user : receive_notification
    users ||--o{ post : creates

    conversation ||--|| message : contains
    group_conversation ||--|| group_conversation_users : contains
    message ||--o commission : for_commission
    messenger_messages ||--|| message : message
    notification ||--|| notification_user : send_to
    post ||--o commission : in_commission
    user_commission ||--o commission : member
