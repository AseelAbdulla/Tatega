🌿 Tatega — تعتيقة

«منصة إلكترونية لبيع المحاصيل الطبيعية والمنتجات التقليدية»

Tatega (تعتيقة) هو نظام تجارة إلكترونية يهدف إلى رقمنة عملية عرض وبيع المحاصيل الطبيعية والمنتجات التقليدية، من خلال توفير Backend متكامل مبني باستخدام Laravel وRESTful APIs.

يوفر النظام البنية الخلفية اللازمة لإدارة المنتجات والتصنيفات والمخزون والطلبات والعملاء والعناوين والتقييمات، بالإضافة إلى نظام متكامل لإدارة المستخدمين والأدوار والصلاحيات.

يعمل هذا المستودع كـ Backend مستقل، ويوفر RESTful API يمكن استهلاكها من أي تطبيق أو واجهة مستخدم متوافقة مع الـAPI.

---

📌 محتويات المستودع

- 🔐 نظام المصادقة وتسجيل المستخدمين.
- 👥 إدارة المستخدمين.
- 🛡️ نظام الأدوار والصلاحيات.
- 📦 إدارة المنتجات.
- 🗂️ إدارة التصنيفات.
- ⚖️ إدارة وحدات المنتجات والأسعار.
- 🖼️ إدارة صور المنتجات.
- 📊 إدارة المخزون.
- 🛒 إدارة سلة المشتريات.
- 📍 إدارة عناوين العملاء.
- 🧾 إدارة الطلبات.
- ⭐ إدارة تقييمات المنتجات.
- 📊 إحصائيات لوحة التحكم.
- 🔔 الإشعارات الداخلية.
- 🖼️ إدارة البانرات والمميزات والشركاء.
- ⚙️ إدارة إعدادات النظام.
- 🌍 دعم البيانات متعددة اللغات.

---

🛠️ التقنيات المستخدمة

التقنية| الاستخدام
🐘 PHP| لغة البرمجة الأساسية
🚀 Laravel 12| إطار عمل الـBackend
🔐 Laravel Sanctum| المصادقة وحماية واجهات API
🌐 RESTful API| توفير الخدمات والبيانات للتطبيقات العميلة
🗄️ MySQL| نظام إدارة قاعدة البيانات
🧩 Eloquent ORM| التعامل مع قاعدة البيانات
👥 Spatie Laravel Permission| إدارة الأدوار والصلاحيات
🌍 Spatie Laravel Translatable| دعم البيانات متعددة اللغات
🧪 PHPUnit| الاختبارات الآلية
📦 Composer| إدارة حزم PHP

«ملاحظة: يعتمد المشروع حاليًا على Laravel 12 وPHP 8.2 أو أحدث، وفقًا لإعدادات "composer.json".»

---

🏗️ بنية النظام

يعتمد المشروع على بنية Laravel التقليدية مع فصل مسؤوليات النظام إلى عدة طبقات:

Tatega
│
├── app/
│   ├── Enums/
│   ├── Http/
│   │   └── Controllers/
│   ├── Mail/
│   ├── Models/
│   ├── Providers/
│   └── Services/
│
├── bootstrap/
├── config/
│
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│
├── public/
├── resources/
│   └── views/
│
├── routes/
│   ├── api.php
│   ├── auth.php
│   ├── console.php
│   └── web.php
│
├── storage/
├── tests/
│
├── .env.example
├── artisan
├── composer.json
└── composer.lock

---

✨ المميزات الرئيسية

🛍️ مميزات العملاء

📚 تصفح المنتجات

يوفر النظام API عامة تتيح الوصول إلى المنتجات والتصنيفات ومعلومات المنتجات.

يمكن للمستخدم:

- عرض المنتجات.
- عرض تفاصيل المنتج.
- تصفح التصنيفات.
- عرض تفاصيل التصنيف.
- عرض صور المنتجات.
- عرض وحدات القياس.
- عرض التقييمات المتاحة.

---

⚖️ التسعير متعدد الوحدات

يدعم النظام إمكانية ارتباط المنتج بأكثر من وحدة قياس.

على سبيل المثال، يمكن أن يكون المنتج متاحًا بوحدات مختلفة مثل:

كيلوغرام
نصف كيلو
ربع كيلو
عبوة

ويتم التعامل مع وحدات المنتج من خلال نموذج مستقل:

Product
   │
   └── ProductUnit

مما يجعل النظام أكثر مرونة في التعامل مع المنتجات التي تختلف طريقة بيعها أو تسعيرها حسب الوحدة.

---

🛒 سلة المشتريات

يوفر النظام للمستخدمين المسجلين نظامًا متكاملًا لإدارة سلة المشتريات.

تشمل العمليات:

- إضافة منتج إلى السلة.
- عرض محتويات السلة.
- معرفة عدد العناصر في السلة.
- تعديل كمية المنتج.
- حذف عنصر من السلة.
- تفريغ السلة بالكامل.

---

📍 عناوين العملاء

يمكن للمستخدم إدارة عناوينه لاستخدامها أثناء إنشاء الطلبات.

وتوجد بنية مستقلة للعناوين مرتبطة بالمستخدم، مما يسمح بدعم أكثر من عنوان للمستخدم الواحد.

---

🧾 الطلبات

يدعم النظام دورة أساسية لإدارة الطلبات، وتشمل:

- إنشاء طلب.
- عرض طلبات المستخدم.
- عرض تفاصيل الطلب.
- إلغاء الطلب وفق حالة الطلب وقواعد النظام.
- إدارة الطلبات من جانب الإدارة.
- تحديث حالة الطلب.

---

⭐ تقييم المنتجات

يوفر النظام إمكانية تقييم المنتجات من قبل المستخدمين.

كما يمكن للإدارة التحكم في التقييمات من خلال:

- عرض التقييمات.
- تعديل التقييم.
- حذف التقييم.
- التحكم في حالة التقييم.

---

🧑‍💼 مميزات الإدارة

📦 إدارة المنتجات

يمكن للمسؤول إدارة المنتجات من خلال عمليات CRUD كاملة:

- إنشاء منتج.
- عرض المنتجات.
- عرض تفاصيل منتج.
- تعديل منتج.
- حذف منتج.

كما يمكن إدارة:

- صور المنتجات.
- وحدات المنتجات.
- الأسعار.
- الخصومات.
- حالة المنتج.
- المخزون.

---

🗂️ إدارة التصنيفات

يوفر النظام إدارة كاملة للتصنيفات:

Create
Read
Update
Delete

ويمكن ربط المنتجات بالتصنيفات المناسبة لتنظيم عملية عرض المنتجات.

---

🖼️ إدارة صور المنتجات

يدعم النظام إدارة صور المنتجات بشكل مستقل.

يمكن للمسؤول:

- إضافة صورة.
- عرض الصور.
- تعديل بيانات الصورة.
- حذف الصورة.

ويتيح ذلك فصل بيانات المنتج الأساسية عن الملفات والصور المرتبطة به.

---

📊 إدارة المخزون

يحتوي المنتج على بيانات مرتبطة بالمخزون، منها:

stock
low_stock_threshold
status

ويتيح ذلك بناء نظام لمتابعة الكميات وتنبيه الإدارة عند انخفاض المخزون عن الحد المحدد.

---

🧾 إدارة الطلبات

توفر الإدارة واجهات API مخصصة للتعامل مع الطلبات، وتشمل:

- عرض جميع الطلبات.
- عرض تفاصيل الطلب.
- إدارة بيانات الطلب.
- تحديث حالة الطلب.
- متابعة حالة الطلبات.

---

👥 إدارة المستخدمين

يمكن للمسؤول إدارة المستخدمين من خلال:

- عرض المستخدمين.
- عرض تفاصيل مستخدم.
- إنشاء مستخدم.
- تعديل مستخدم.
- حذف مستخدم.

---

🛡️ الأدوار والصلاحيات

يعتمد المشروع على حزمة:

Spatie Laravel Permission

لتطبيق نظام Role-Based Access Control (RBAC).

ويتم تنظيم الصلاحيات بالشكل التالي:

User
 │
 └── Role
      │
      └── Permissions

وتشمل الصلاحيات عمليات مثل:

view-users
create-users
update-users
delete-users

view-roles
create-roles
update-roles
delete-roles

view-orders

manage-banners
manage-features
manage-partners
manage-settings

يسمح هذا النظام بإعطاء كل مستخدم أو دور الصلاحيات المناسبة له دون الحاجة إلى ربط الصلاحيات مباشرة بكل عملية داخل النظام.

---

📊 إحصائيات لوحة التحكم

يوفر النظام Endpoint مخصصًا لإحصائيات لوحة التحكم:

GET /api/admin/dashboard/stats

ويمكن استخدام هذه البيانات لبناء لوحة معلومات تعرض مؤشرات النظام الرئيسية.

---

🔐 المصادقة والأمان

يستخدم المشروع Laravel Sanctum للمصادقة وحماية المسارات الخاصة بالمستخدمين.

Authentication Flow

Client
   │
   ├── Register
   │
   ├── Login
   │
   ▼
Laravel Sanctum
   │
   ├── Authentication
   └── Authorization
   │
   ▼
Protected API

مسارات المصادقة الأساسية

POST /api/register
POST /api/login
GET  /api/me
POST /api/logout

ويتم حماية المسارات الخاصة بالمستخدمين باستخدام:

auth:sanctum

---

🌐 RESTful API

يعتمد المشروع على RESTful API لتوفير الموارد والعمليات المختلفة.

أمثلة

المنتجات

GET    /api/products
GET    /api/products/{id}
POST   /api/products
PUT    /api/products/{id}
PATCH  /api/products/{id}
DELETE /api/products/{id}

التصنيفات

GET    /api/categories
GET    /api/categories/{id}
POST   /api/categories
PUT    /api/categories/{id}
DELETE /api/categories/{id}

صور المنتجات

GET    /api/product-images
GET    /api/product-images/{id}
POST   /api/product-images
PUT    /api/product-images/{id}
DELETE /api/product-images/{id}

وحدات المنتجات

GET    /api/product-units
GET    /api/product-units/{id}
POST   /api/product-units
PUT    /api/product-units/{id}
DELETE /api/product-units/{id}

---

📡 API Endpoints

🔓 Public Endpoints

Authentication

POST /api/register
POST /api/login

Products

GET /api/products
GET /api/products/{id}

Categories

GET /api/categories
GET /api/categories/{id}

Product Images

GET /api/product-images
GET /api/product-images/{id}

Product Units

GET /api/product-units
GET /api/product-units/{id}

Reviews

GET /api/reviews
GET /api/reviews/{id}

Other Resources

GET /api/banners
GET /api/features
GET /api/partners
GET /api/settings

---

🔒 Protected Endpoints

بعد تسجيل الدخول، يمكن للمستخدم الوصول إلى الخدمات الخاصة به:

GET    /api/me
POST   /api/logout

GET    /api/cart
GET    /api/cart/count
POST   /api/cart/items
PUT    /api/cart/items/{id}
DELETE /api/cart/items/{id}
DELETE /api/cart/clear

POST   /api/orders
GET    /api/orders
GET    /api/orders/{id}
PATCH  /api/orders/{id}/cancel

POST   /api/reviews

---

🔐 Admin Endpoints

توجد مجموعة من المسارات المخصصة للإدارة تحت:

/api/admin

ومن أمثلتها:

المستخدمون

GET    /api/admin/users
POST   /api/admin/users
PUT    /api/admin/users/{id}
DELETE /api/admin/users/{id}

الأدوار

GET    /api/admin/roles
POST   /api/admin/roles
PUT    /api/admin/roles/{id}
DELETE /api/admin/roles/{id}

الصلاحيات

GET /api/admin/permissions

الطلبات

GET   /api/admin/orders
GET   /api/admin/orders/{id}
PATCH /api/admin/orders/{id}/status

الإحصائيات

GET /api/admin/dashboard/stats

«الوصول إلى هذه المسارات يعتمد على المصادقة والصلاحيات المحددة للمستخدم.»

---

🗄️ قاعدة البيانات

يعتمد المشروع على MySQL مع استخدام Laravel Migrations وEloquent ORM.

أهم الكيانات

Model| المسؤولية
"User"| المستخدمون
"Role"| الأدوار
"Permission"| الصلاحيات
"Address"| عناوين المستخدمين
"Category"| تصنيفات المنتجات
"Product"| المنتجات
"ProductImage"| صور المنتجات
"ProductUnit"| وحدات المنتجات
"Review"| تقييمات المنتجات
"Cart"| سلة المشتريات
"CartDetail"| عناصر السلة
"Order"| الطلبات
"OrderDetail"| تفاصيل الطلبات
"Banner"| البانرات
"Feature"| مميزات المنصة
"Partner"| الشركاء
"Setting"| إعدادات النظام
"InternalNotification"| الإشعارات الداخلية

---

🔗 العلاقات الأساسية

يمكن تبسيط العلاقات الأساسية للنظام بالشكل التالي:

User
 │
 ├── Addresses
 │
 ├── Cart
 │    └── Cart Details
 │
 ├── Orders
 │    └── Order Details
 │
 └── Reviews


Category
 │
 └── Products
       │
       ├── Product Images
       ├── Product Units
       └── Reviews

---

🌍 دعم تعدد اللغات

يستخدم المشروع:

spatie/laravel-translatable

لدعم تخزين البيانات متعددة اللغات.

ومن البيانات التي يمكن تخزينها بصيغة متعددة اللغات:

name
description

مثال:

{
    "ar": "اسم المنتج",
    "en": "Product Name"
}

يسمح هذا التصميم بإضافة لغات جديدة مستقبلًا دون الحاجة إلى إنشاء أعمدة منفصلة لكل لغة.

---

⚙️ متطلبات التشغيل

قبل تثبيت المشروع، تأكد من توفر المتطلبات التالية:

المتطلب| الإصدار المطلوب
🐘 PHP| ">= 8.2"
📦 Composer| الإصدار الحديث
🗄️ MySQL| الإصدار المتوافق مع Laravel
🌐 Git| الإصدار الحديث

---

🚀 التثبيت والتشغيل

1️⃣ استنساخ المستودع

git clone https://github.com/AseelAbdulla/Tatega.git
cd Tatega

---

2️⃣ تثبيت Dependencies

composer install

---

3️⃣ إنشاء ملف البيئة

Linux / macOS

cp .env.example .env

Windows

copy .env.example .env

---

4️⃣ إنشاء Application Key

php artisan key:generate

---

5️⃣ إنشاء قاعدة البيانات

أنشئ قاعدة بيانات جديدة في MySQL، مثل:

tatega

ثم افتح ملف ".env" وأدخل بيانات الاتصال:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tatega
DB_USERNAME=root
DB_PASSWORD=

«قم بتعديل "DB_USERNAME" و"DB_PASSWORD" وفق إعدادات MySQL الموجودة لديك.»

---

6️⃣ تشغيل Migrations وSeeders

لتجهيز قاعدة البيانات وإضافة البيانات التجريبية:

php artisan migrate --seed

أو لإعادة إنشاء قاعدة البيانات بالكامل:

php artisan migrate:fresh --seed

⚠️ تنبيه: الأمر "migrate:fresh" يقوم بحذف الجداول الحالية وإعادة إنشائها، لذلك لا تستخدمه على قاعدة بيانات تحتوي على بيانات مهمة.

---

7️⃣ إنشاء رابط Storage

php artisan storage:link

---

8️⃣ تشغيل Laravel

php artisan serve

سيصبح الـBackend متاحًا افتراضيًا على:

http://127.0.0.1:8000

وتصبح الـAPI متاحة على:

http://127.0.0.1:8000/api

---

⚡ إعداد سريع

يوفر المشروع Script باسم:

composer setup

والذي يساعد في تنفيذ مجموعة من خطوات الإعداد الأساسية للمشروع.

كما يمكن استخدام:

composer dev

لتشغيل بيئة التطوير وفق إعدادات المشروع.

---

🧪 الاختبارات

لتشغيل اختبارات المشروع:

php artisan test

أو:

composer test

---

🧹 أوامر مفيدة أثناء التطوير

تنظيف Cache

php artisan optimize:clear

إنشاء رابط التخزين

php artisan storage:link

إعادة تشغيل قاعدة البيانات

php artisan migrate:fresh --seed

تشغيل السيرفر

php artisan serve

تشغيل الاختبارات

php artisan test

---

👤 بيانات الحساب الافتراضي

حساب المسؤول للاختبار

البيان| القيمة
📧 البريد الإلكتروني| "admin@example.com"
🔑 كلمة المرور| "password123"
👤 نوع الحساب| "Admin"

«⚠️ ملاحظة: يجب التأكد من أن هذه البيانات موجودة في Seeder المستخدمين في النسخة المعتمدة من المشروع. إذا لم يكن الحساب موجودًا، يمكن إنشاء حساب المسؤول من خلال Seeder مخصص قبل استخدامه للاختبار.»

---

🔄 دورة الطلب

يمكن تصور دورة عملية الشراء بالشكل التالي:

┌─────────────────────┐
│  تصفح المنتجات 🛍️  │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ اختيار المنتج 📦    │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ اختيار الوحدة ⚖️    │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ إضافة إلى السلة 🛒 │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ تحديد العنوان 📍    │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ إنشاء الطلب 🧾     │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ إدارة الطلب 👨‍💼    │
└─────────────────────┘

---

🔐 نموذج الصلاحيات

يعتمد النظام على Role-Based Access Control (RBAC):

                    ┌──────────────┐
                    │     User     │
                    └──────┬───────┘
                           │
                           ▼
                    ┌──────────────┐
                    │     Role     │
                    └──────┬───────┘
                           │
                           ▼
                 ┌───────────────────┐
                 │   Permissions     │
                 └─────────┬─────────┘
                           │
              ┌────────────┼────────────┐
              ▼            ▼            ▼
           View         Create        Update
                                      │
                                      ▼
                                    Delete

يساعد هذا التصميم على التحكم في الوصول إلى وظائف النظام بطريقة منظمة وقابلة للتوسع.

---

📁 تنظيم الكود

يعتمد المشروع على تنظيم Laravel القياسي:

"app/Models"

يحتوي على Eloquent Models التي تمثل كيانات النظام.

"app/Http/Controllers"

يحتوي على Controllers المسؤولة عن استقبال طلبات API وتنفيذ العمليات المطلوبة.

"app/Services"

يحتوي على طبقة مخصصة لمنطق الأعمال الذي يمكن فصله عن Controllers.

"app/Enums"

يحتوي على القيم الثابتة المستخدمة في النظام.

"database/migrations"

يحتوي على مخططات إنشاء وتعديل جداول قاعدة البيانات.

"database/seeders"

يحتوي على البيانات الأولية والبيانات التجريبية.

"routes/api.php"

يحتوي على تعريف RESTful API الخاصة بالمشروع.

---

📌 قواعد تطوير API

عند إضافة Endpoint جديد، يفضل الالتزام بالمبادئ التالية:

- استخدام HTTP Methods المناسبة.
- استخدام أسماء موارد واضحة.
- حماية المسارات الخاصة بالمستخدمين باستخدام Sanctum.
- حماية مسارات الإدارة بالصلاحيات المناسبة.
- التحقق من صحة البيانات باستخدام Form Requests أو Validation.
- إرجاع HTTP Status Codes مناسبة.
- توحيد شكل استجابات API.
- عدم كشف البيانات الحساسة في Responses.
- توثيق أي Endpoint جديد.

---

🚧 التطوير المستقبلي

يمكن توسيع المشروع مستقبلًا بإضافة:

- 🔎 البحث المتقدم عن المنتجات.
- 🏷️ نظام العروض والخصومات.
- 💳 بوابات الدفع الإلكتروني.
- 🚚 إدارة عمليات التوصيل والشحن.
- 📊 تقارير متقدمة للمبيعات.
- 📈 تقارير المخزون.
- 🔔 نظام إشعارات أكثر تطورًا.
- 📧 إشعارات البريد الإلكتروني.
- 🧪 زيادة تغطية الاختبارات الآلية.
- 📚 توثيق API باستخدام OpenAPI / Swagger.
- ⚡ تحسين الأداء والاستعلامات.
- 🔐 تعزيز سياسات الأمان.
- 📦 تحسين إدارة المخزون وحركة المنتجات.

---

🔗 المستودع

Tatega — تعتيقة

منصة تجارة إلكترونية للمحاصيل الطبيعية والمنتجات التقليدية.

GitHub:

https://github.com/AseelAbdulla/Tatega

---

<p align="center">🌿 Tatega — تعتيقة

Natural Products & Traditional Goods E-Commerce Backend

</p>
