```mermaid
erDiagram
    USER {
        int id
        varchar(180) username
        varchar(255) phonenumber
        json roles
        varchar(180) email
        varchar(255) password
    }
    COMMISSION {
        int id
        varchar(255) name
    }
    USER_COMMISSION {
        int user_id
        int commission_id
    }
    POST {
        int id
        int author_id
        int commission_id
        longtext content
        datetime created_at
    }
    CONVERSATION {
        int id
        int user_id
        int message_id
    }
    MESSAGE {
        int id
        int sender_id
        int commission_id
        longtext content
        tinyint(1) is_global
    }
    NOTIFICATION {
        int id
        int message_id
        longtext message_content
        datetime created_at
    }
    NOTIFICATION_USER {
        int notification_id
        int user_id
    }
    GROUP_CONVERSATION {
        int id
        varchar(255) name
    }
    GROUP_CONVERSATION_USERS {
        int user_id
        int group_conversation_id
    }
    MESSENGER_MESSAGES {
        bigint id
        longtext body
        longtext headers
        varchar(190) queue_name
        datetime created_at
        datetime available_at
        datetime delivered_at
    }
    
    USER ||--o{ USER_COMMISSION : "belongsTo"
    USER ||--o{ POST : "authored"
    USER ||--o{ CONVERSATION : "initiated"
    USER ||--o{ MESSAGE : "sent"
    USER ||--o{ NOTIFICATION_USER : "received"
    
    COMMISSION ||--o{ USER_COMMISSION : "includes"
    COMMISSION ||--o{ POST : "belongs to"
    COMMISSION ||--o{ MESSAGE : "belongs to"
    
    MESSAGE ||--o{ CONVERSATION : "part of"
    MESSAGE ||--o{ NOTIFICATION : "generates"
    
    NOTIFICATION ||--o{ NOTIFICATION_USER : "sent to"
    
    GROUP_CONVERSATION ||--o{ GROUP_CONVERSATION_USERS : "includes"
    
    USER_COMMISSION ||--o{ USER : "memberOf"
    POST ||--o{ USER : "authored by"
    CONVERSATION ||--o{ USER : "initiated by"
    MESSAGE ||--o{ USER : "sent by"
    NOTIFICATION_USER ||--o{ USER : "received by"
    
    USER_COMMISSION ||--o{ COMMISSION : "part of"
    POST ||--o{ COMMISSION : "belongs to"
    MESSAGE ||--o{ COMMISSION : "associated with"
    
    CONVERSATION ||--o{ MESSAGE : "contains"
    
    NOTIFICATION ||--o{ MESSAGE : "based on"
    
    GROUP_CONVERSATION_USERS ||--o{ GROUP_CONVERSATION : "included in"
