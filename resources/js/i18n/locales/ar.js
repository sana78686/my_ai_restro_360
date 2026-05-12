/**
 * Arabic locale overrides merged onto `en.js` (see `i18n/index.js`).
 * Any key omitted here stays English from the base locale after merge.
 */
export default {
    dashboard: 'لوحة التحكم',
    orders: 'الطلبات',
    stockCheckRequests: 'طلبات جرد المخزون',
    quoteRequests: 'طلبات عروض الأسعار',
    reservations: 'الحجوزات',
    customers: 'العملاء',
    bulletin: 'الإعلانات',
    categories: 'فئات القائمة',
    category: 'فئة القائمة',
    content_system: 'نظام المحتوى',
    products: 'أصناف القائمة',
    roles: 'الأدوار',
    users: 'المستخدمون',
    mail_logs: 'سجلات البريد',
    reset_password: 'تغيير كلمة المرور',
    restaurants: 'المطاعم',
    settings: 'إعدادات المطعم',
    personal_settings: 'الإعدادات الشخصية',
    logout: 'تسجيل الخروج',
    profile: 'الملف الشخصي',
    role: 'دور',
    contact: 'اتصل بنا',
    privacy: 'الخصوصية',
    terms: 'الشروط',
    aboutText:
        'نوفر حلولاً شاملة لإدارة المطاعم لمساعدة أعمالك على النمو والنجاح في العصر الرقمي.',
    passwordChange: 'تغيير كلمة المرور',
    restaurantSettings: 'إعدادات المطعم',
    notifications: 'الإشعارات',
    subscribers: 'المشتركون',
    emailSetting: 'إعدادات البريد',
    paymentGateways: 'بوابات الدفع',
    manageSubscriptions: 'إدارة الاشتراك',
    contactReqs: 'طلبات الاتصال',
    stockCheck: 'جرد المخزون',
    logoutSuccess: 'تم تسجيل الخروج بنجاح',
    youHaveBeenLoggedOut: 'تم تسجيل خروجك من حسابك.',
    logoutFailed: 'فشل تسجيل الخروج',
    somethingWentWrong: 'حدث خطأ ما، يرجى المحاولة مرة أخرى.',
    changeLanguage: 'تغيير اللغة',
    businessEmail: 'البريد التجاري',

    cart: {
        title: 'سلة التسوق',
        Total: 'الإجمالي:',
        Checkout: 'الدفع',
        empty: 'سلة التسوق فارغة.'
    },

    welcome: 'مرحباً بك في Ai Restro 360',
    subtitle: 'حلّك الأمثل لإدارة المطاعم',
    getStarted: 'ابدأ الآن',
    learnMore: 'اعرف المزيد',
    whyChoose: 'لماذا Ai Restro 360؟',

    welcomeBack: 'مرحباً!',
    getInTouch: 'تواصل معنا',
    created: 'تاريخ الإنشاء',

    dashboard: {
        recentTenants: 'أحدث المطاعم',
        systemStats: 'إحصائيات النظام'
    },

    main_dashboard: {
        dashboard: 'لوحة التحكم',
        stats: {
            totalTenants: 'إجمالي المطاعم',
            activeTenants: 'المطاعم النشطة',
            pendingTenants: 'في انتظار الموافقة',
            suspendedTenants: 'المطاعم المعلّقة',
            totalUsers: 'إجمالي المستخدمين',
            totalRoles: 'إجمالي الأدوار',
            totalPermissions: 'إجمالي الصلاحيات',
            activeUsers: 'المستخدمون النشطون اليوم'
        },
        quickActions: {
            title: 'إجراءات سريعة',
            manageTenants: 'إدارة المطاعم',
            manageUsers: 'إدارة المستخدمين',
            manageRoles: 'إدارة الأدوار',
            clearCache: 'مسح الذاكرة المؤقتة'
        },
        pulse: {
            title: 'النبض',
            insights: 'رؤى',
            overview: 'نظرة عامة',
            searchPlaceholder: 'ابحث عن مطعم أو مالك أو حالة…',
            tenantsScope: '{count} مطاعم',
            healthScore: 'صحة المنصة',
            healthAverage: '/100 متوسط',
            suspendedHint: '{count} تحتاج اهتماماً',
            pendingHint: '{count} بانتظار المراجعة',
            activeShare: '{pct}% من جميع المطاعم',
            userPulse: 'حجم الدليل',
            userHint: '{count} أدوار مُعدّة',
            trendTitle: 'نشاط المنصة',
            trendSubtitle: 'المطاعم والمستخدمون (مؤشر مُركّب)',
            recentTitle: 'تسجيلات اليوم',
            systemTitle: 'النظام',
            quickTitle: 'اختصارات'
        }
    },

    tenants: {
        pageTitle: 'إدارة المطاعم',
        approveOwnerDialog: {
            title: 'التحقق من مالك المطعم؟',
            text: 'يمكن للمالك تسجيل الدخول دون رمز بريد إلكتروني لمرة واحدة. سيتم إرسال بريد تأكيد إلى عنوانه.',
            confirm: 'نعم، تحقق',
            cancel: 'إلغاء'
        },
        approveOwnerSuccess:
            'تم التحقق من المالك. تم إرسال بريد تأكيد إلى صاحب المطعم.',
        deleteConfirmTitle: 'حذف هذا المطعم؟',
        deleteConfirmText:
            'سيتم حذف مطعم "{name}" نهائياً: قاعدة بيانات المستأجر والنطاقات والاشتراكات وجميع الملفات المخزنة في السحابة. لا يمكن التراجع.',
        deleteConfirmButton: 'نعم، احذف نهائياً',
        deleteSuccess:
            'تم حذف المطعم بنجاح. تمت إزالة قاعدة بيانات المستأجر وملفات السحابة.',
        deleteFailed: 'تعذّر حذف المطعم.',
        list: {
            title: 'المطاعم',
            name: 'الاسم',
            domain: 'النطاق',
            owner: 'المالك',
            status: 'الحالة',
            ownerLogin: 'دخول المالك',
            approveOwner: 'الموافقة على المالك',
            ownerApproved: 'موافَق عليه',
            verification: 'التحقق',
            needVerification: 'بحاجة إلى تحقق',
            ownerVerified: 'تم التحقق',
            actions: 'الإجراءات',
            noImage: 'لا توجد صورة'
        },
        status: {
            active: 'نشط',
            pending: 'معلّق',
            suspended: 'موقوف',
            trial: 'تجريبي',
            inactive: 'غير نشط'
        },
        filters: {
            searchPlaceholder: 'البحث في المطاعم…',
            allStatus: 'كل الحالات'
        },
        empty: {
            title: 'لا توجد مطاعم',
            filtered: 'لا توجد مطاعم مطابقة لبحثك. جرّب تعديل المرشحات.',
            noData: 'لم يُسجّل أي مطعم بعد.'
        },
        actions: {
            view: 'عرض',
            edit: 'تعديل',
            delete: 'حذف',
            approve: 'موافقة',
            suspend: 'إيقاف',
            activate: 'تفعيل'
        }
    },

    common: {
        viewAll: 'عرض الكل',
        hone: 'الرئيسية',
        verifying: 'جارٍ التحقق...',
        submit: 'إرسال',
        save: 'حفظ',
        cancel: 'إلغاء',
        close: 'إغلاق',
        delete: 'حذف',
        edit: 'تعديل',
        error: 'خطأ',
        create: 'إنشاء',
        description: 'الوصف',
        usersCount: 'عدد المستخدمين',
        search: 'بحث',
        actions: 'الإجراءات',
        confirm: 'تأكيد',
        success: 'نجاح',
        loading: 'جارٍ التحميل...',
        noData: 'لا توجد بيانات',
        update: 'تحديث',
        processing: 'جارٍ المعالجة...',
        home: 'الرئيسية'
    },

    messages: {
        confirmDelete: 'هل أنت متأكد أنك تريد حذف هذا العنصر؟',
        deleteSuccess: 'تم حذف العنصر بنجاح',
        savingChanges: 'جارٍ حفظ التغييرات...',
        changesSaved: 'تم حفظ التغييرات بنجاح',
        errorOccurred: 'حدث خطأ',
        sessionExpired: 'انتهت جلستك. يرجى تسجيل الدخول مرة أخرى.',
        unauthorized: 'غير مصرّح لك بتنفيذ هذا الإجراء.'
    },

    roles_module: {
        title: 'الأدوار والصلاحيات',
        description: 'إدارة أدوار النظام وصلاحياتها',
        addNew: 'إضافة دور',
        editRole: 'تعديل الدور',
        addRole: 'إضافة دور جديد',
        noRolesFound: 'لا توجد أدوار',
        noPermissionsFound: 'لا توجد صلاحيات',
        more: 'المزيد'
    },

    auth: {
        turnstile: {
            required: 'يرجى إكمال التحقق الأمني.'
        },
        login: {
            title: 'تسجيل الدخول إلى حسابك',
            email: 'البريد الإلكتروني',
            password: 'كلمة المرور',
            remember: 'تذكّرني',
            submit: 'تسجيل الدخول',
            submitting: 'جارٍ تسجيل الدخول...',
            forgot: 'نسيت كلمة المرور؟',
            noAccount: 'ليس لديك حساب؟',
            register: 'سجّل الآن',
            showPassword: 'إظهار',
            hidePassword: 'إخفاء'
        },
        superAdmin: {
            title: 'تسجيل الدخول',
            subtitle: 'استخدم البريد وكلمة المرور لحساب AiRestro360.',
            signInWithGoogle: 'تسجيل الدخول عبر Google',
            orContinueEmail: 'أو المتابعة بالبريد',
            unifiedTitle: 'تسجيل الدخول',
            unifiedSubtitle: 'أدخل البريد المرتبط بمطعمك أو بملف AiRestro360.',
            unifiedDetail:
                'يمكن أن يكون لكل مطعم عنواناً خاصاً. سنوجّهك تلقائياً إلى صفحة الدخول الصحيحة.',
            continue: 'متابعة',
            passwordStepTitle: 'أدخل كلمة المرور',
            passwordStepSubtitle: 'تسجيل الدخول كـ {email}',
            changeEmail: 'استخدام بريد آخر',
            emailRequired: 'يرجى إدخال البريد الإلكتروني.',
            emailInvalid: 'يرجى إدخال بريد إلكتروني صالح.',
            passwordRequired: 'يرجى إدخال كلمة المرور.',
            lookupNotFound: 'لا يوجد حساب لهذا البريد.',
            lookupUnexpected: 'حدث خطأ. يرجى المحاولة مرة أخرى.'
        }
    }
}
