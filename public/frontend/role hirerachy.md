# role hierachy

┌─────────────────────────────────────────────────────────────────┐
│                      SUPER ADMIN                               │
│                    (System Owner)                               │
│  • Full system access                                           │
│  • Can do EVERYTHING                                            │
│  • Can create/delete ANY user                                  │
│  • Can assign ANY role (including SuperAdmin)                  │
│  • Can delete ANY content                                      │
│  • Can manage ALL sections                                     │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                        ADMIN                                    │
│                    (Team Manager)                               │
│  • Can manage ALL content (projects, skills, blog, etc.)       │
│  • Can view messages                                            │
│  • Can view users (but NOT create/edit/delete)                 │
│  • CANNOT create/delete users                                  │
│  • CANNOT assign SuperAdmin role                               │
│  • CANNOT delete SuperAdmin users                              │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       EDITOR                                    │
│                   (Content Creator)                             │
│  • Can create/edit/delete ALL content sections                 │
│  • Can publish blog posts                                      │
│  • CANNOT manage users                                         │
│  • CANNOT access Users or Messages sections                    │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       AUTHOR                                    │
│                  (Blog Writer)                                  │
│  • Can create/edit blog posts (DRAFTS only)                    │
│  • CANNOT publish blog posts (needs Editor/Admin approval)     │
│  • CANNOT edit other content sections                          │
│  • CANNOT manage users                                         │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       VIEWER                                    │
│                  (Read-Only Access)                             │
│  • Can view dashboard stats                                    │
│  • Can view portfolio preview                                  │
│  • Can edit their OWN profile                                  │
│  • CANNOT create/edit/delete ANY content                       │
│  • CANNOT manage users                                         │
└─────────────────────────────────────────────────────────────────┘