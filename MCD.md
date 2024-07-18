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
    MESSENGER_MESSAGES {
        bigint id
        longtext body
        longtext headers
        varchar(190) queue_name
        datetime created_at
        datetime available_at
        datetime delivered_at
    }
    
    USER ||--o{ USER_COMMISSION : has
    USER ||--o{ POST : authors
    USER ||--o{ CONVERSATION : initiates
    USER ||--o{ MESSAGE : sends
    USER ||--o{ NOTIFICATION_USER : receives
    
    COMMISSION ||--o{ USER_COMMISSION : includes
    COMMISSION ||--o{ POST : has
    COMMISSION ||--o{ MESSAGE : includes
    
    MESSAGE ||--o{ CONVERSATION : part of
    MESSAGE ||--o{ NOTIFICATION : generates
    
    NOTIFICATION ||--o{ NOTIFICATION_USER : sent to
    
    GROUP_CONVERSATION ||--o{ GROUP_CONVERSATION_USERS : includes
    
    USER_COMMISSION ||--o{ USER : member of
    POST ||--o{ USER : authored by
    CONVERSATION ||--o{ USER : initiated by
    MESSAGE ||--o{ USER : sent by
    NOTIFICATION_USER ||--o{ USER : received by
    
    USER_COMMISSION ||--o{ COMMISSION : part of
    POST ||--o{ COMMISSION : belongs to
    MESSAGE ||--o{ COMMISSION : associated with
    
    CONVERSATION ||--o{ MESSAGE : contains
    
    NOTIFICATION ||--o{ MESSAGE : based on
    
    GROUP_CONVERSATION_USERS ||--o{ GROUP_CONVERSATION : included in
