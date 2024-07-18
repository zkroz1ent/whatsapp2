erDiagram
    USER {
        id int [PK]
        username varchar
        phonenumber varchar
        roles json
        password varchar
        email varchar
        created_at timestamp
    }

    POST {
        id int [PK]
        title varchar
        body text
        user_id int [FK]
        status varchar
        created_at timestamp
    }

    FOLLOW {
        following_user_id int [FK]
        followed_user_id int [FK]
        created_at timestamp 
    }

    COMMISSION {
        id int [PK]
        name varchar
    }

    CONVERSATION {
        id int [PK]
        user_id int [FK]
        message_id int [FK]
    }

    MIGRATION_VERSION {
        version varchar(191) [PK]
        executed_at datetime
        execution_time int
    }

    GROUP_CONVERSATION {
        id int [PK]
        name varchar
    }

    GROUP_CONVERSATION_USER {
        user_id int [FK]
        group_conversation_id int [FK]
    }

    MESSAGE {
        id int [PK]
        sender_id int [FK]
        commission_id int [FK]
        content longtext
        is_global tinyint
    }

    MESSENGER_MESSAGE {
        id bigint [PK]
        body longtext
        headers longtext
        queue_name varchar(190)
        created_at datetime
        available_at datetime
        delivered_at datetime
    }

    NOTIFICATION {
        id int [PK]
        message_id int [FK]
        message_content longtext
        created_at datetime
    }

    NOTIFICATION_USER {
        notification_id int [FK]
        user_id int [FK]
    }

    // Relationships
    USER ||--o{ POST : "author_id"
    USER ||--o{ FOLLOW : "following_user_id, followed_user_id"
    USER ||--o{ CONVERSATION : "user_id"
    USER ||--o{ GROUP_CONVERSATION_USER : "user_id"
    USER ||--o{ MESSAGE : "sender_id"
    USER ||--o{ NOTIFICATION_USER : "user_id"
    USER ||--o{ POST : "author_id"

    POST ||--o{ POST : "commission_id"

    COMMISSION ||--o{ MESSAGE : "commission_id"
    COMMISSION ||--o{ POST : "commission_id"

    GROUP_CONVERSATION ||--o{ GROUP_CONVERSATION_USER : "id"

    MESSAGE ||--o{ NOTIFICATION : "message_id"

    NOTIFICATION ||--o{ NOTIFICATION_USER : "notification_id"

    // Foreign Keys
    FK_B6BD307F202D1EB2 --> MESSAGE : "commission_id"
    FK_B6BD307FF624B39D --> MESSAGE : "sender_id"
    FK_8A8E26E9537A1329 --> CONVERSATION : "message_id"
    FK_8A8E26E9A76ED395 --> CONVERSATION : "user_id"
    FK_2FE4BE6DA76ED395 --> GROUP_CONVERSATION_USER : "user_id"
    FK_2FE4BE6DB73F9E4F --> GROUP_CONVERSATION_USER : "group_conversation_id"
    FK_BF5476CA537A1329 --> NOTIFICATION : "message_id"
    FK_35AF9D73A76ED395 --> NOTIFICATION_USER : "user_id"
    FK_35AF9D73EF1A9D84 --> NOTIFICATION_USER : "notification_id"
    FK_5A8A6C8D202D1EB2 --> POST : "commission_id"
    FK_5A8A6C8DF675F31B --> POST : "author_id"
