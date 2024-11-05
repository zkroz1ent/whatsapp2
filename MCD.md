```mermaid
erDiagram
    USER {
        int id
        varchar(180) username
        varchar(255) phonenumber
        varchar(180) email
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
    }
    GROUP_CONVERSATION {
        int id
        varchar(255) name
    }
    GROUP_CONVERSATION_USERS {
        int user_id
        int group_conversation_id
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
    
    USER ||--o{ USER_COMMISSION : "belongsTo"
    USER ||--o{ POST : "author"
    USER ||--o{ CONVERSATION : "initiates"
    USER ||--o{ MESSAGE : "sends"
    USER ||--o{ NOTIFICATION_USER : "receives"
    
    COMMISSION ||--o{ USER_COMMISSION : "includes"
    COMMISSION ||--o{ POST : "contains"
    COMMISSION ||--o{ MESSAGE : "related to"
    
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
