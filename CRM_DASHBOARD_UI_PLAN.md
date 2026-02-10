# Hospitality CRM Dashboard UI Redesign Plan

## Overview
This plan outlines the complete redesign of the Hospitality CRM dashboard UI based on the Dribbble design reference (https://dribbble.com/shots/24718258-Project-management-dashboard-Business-Analytics-App).

---

## Phase 1: Design System Foundation

### 1.1 Color Palette
```css
:root {
    /* Primary Colors */
    --primary-50: #eef2ff;
    --primary-100: #e0e7ff;
    --primary-200: #c7d2fe;
    --primary-300: #a5b4fc;
    --primary-400: #818cf8;
    --primary-500: #6366f1;  /* Main primary */
    --primary-600: #4f46e5;
    --primary-700: #4338ca;
    --primary-800: #3730a3;
    --primary-900: #312e81;

    /* Accent Colors */
    --accent-cyan: #06b6d4;
    --accent-teal: #14b8a6;
    --accent-emerald: #10b981;
    --accent-amber: #f59e0b;
    --accent-orange: #f97316;
    --accent-pink: #ec4899;
    --accent-purple: #8b5cf6;
    --accent-indigo: #6366f1;

    /* Semantic Colors */
    --success: #10b981;
    --success-light: #d1fae5;
    --warning: #f59e0b;
    --warning-light: #fef3c7;
    --danger: #ef4444;
    --danger-light: #fee2e2;
    --info: #3b82f6;
    --info-light: #dbeafe;

    /* Neutral Colors */
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;

    /* Background Colors */
    --bg-primary: #ffffff;
    --bg-secondary: #f9fafb;
    --bg-tertiary: #f3f4f6;
    --bg-dark: #111827;

    /* Text Colors */
    --text-primary: #111827;
    --text-secondary: #4b5563;
    --text-tertiary: #9ca3af;
    --text-inverse: #ffffff;
}
```

### 1.2 Typography System
```css
:root {
    /* Font Family */
    --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;

    /* Font Sizes */
    --text-xs: 0.75rem;      /* 12px */
    --text-sm: 0.875rem;     /* 14px */
    --text-base: 1rem;       /* 16px */
    --text-lg: 1.125rem;      /* 18px */
    --text-xl: 1.25rem;      /* 20px */
    --text-2xl: 1.5rem;       /* 24px */
    --text-3xl: 1.875rem;     /* 30px */
    --text-4xl: 2.25rem;      /* 36px */

    /* Font Weights */
    --font-light: 300;
    --font-normal: 400;
    --font-medium: 500;
    --font-semibold: 600;
    --font-bold: 700;
}
```

### 1.3 Spacing System
```css
:root {
    --spacing-1: 0.25rem;   /* 4px */
    --spacing-2: 0.5rem;    /* 8px */
    --spacing-3: 0.75rem;   /* 12px */
    --spacing-4: 1rem;       /* 16px */
    --spacing-5: 1.25rem;    /* 20px */
    --spacing-6: 1.5rem;     /* 24px */
    --spacing-8: 2rem;       /* 32px */
    --spacing-10: 2.5rem;    /* 40px */
    --spacing-12: 3rem;       /* 48px */
    --spacing-16: 4rem;        /* 64px */
}
```

### 1.4 Shadow System
```css
:root {
    --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);

    /* Soft shadows for cards */
    --shadow-soft-sm: 0 2px 8px -2px rgba(0, 0, 0, 0.06), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
    --shadow-soft-md: 0 4px 12px -4px rgba(0, 0, 0, 0.08), 0 8px 16px -8px rgba(0, 0, 0, 0.06);
    --shadow-soft-lg: 0 8px 24px -8px rgba(0, 0, 0, 0.1), 0 16px 32px -16px rgba(0, 0, 0, 0.08);
}
```

### 1.5 Border Radius System
```css
:root {
    --radius-sm: 0.375rem;     /* 6px */
    --radius-md: 0.5rem;        /* 8px */
    --radius-lg: 0.75rem;       /* 12px */
    --radius-xl: 1rem;          /* 16px */
    --radius-2xl: 1.5rem;       /* 24px */
    --radius-full: 9999px;       /* Full round */
}
```

### 1.6 Animation System
```css
:root {
    --transition-fast: 150ms ease;
    --transition-base: 200ms ease;
    --transition-slow: 300ms ease;
    --transition-slower: 500ms ease;

    --ease-in: cubic-bezier(0.4, 0, 1, 1);
    --ease-out: cubic-bezier(0, 0, 0.2, 1);
    --ease-in-out: cubic-bezier(0.4, 0, 0.2, 1);
}
```

---

## Phase 2: Component Library

### 2.1 Card Components
1. **DataCard** - Main content card with header/body/footer
2. **StatCard** - KPI display card with icon, value, label, and trend
3. **ChartCard** - Container for analytics charts
4. **ProfileCard** - User profile summary
5. **FilterCard** - Search and filter controls

### 2.2 Navigation Components
1. **Sidebar** - Collapsible main navigation
2. **Header** - Top navigation with notifications and user menu
3. **Breadcrumb** - Page navigation hierarchy

### 2.3 Table Components
1. **DataTable** - Main data table with sorting/pagination
2. **ActionMenu** - Row-level actions dropdown
3. **StatusBadge** - Status indicators

### 2.4 Chart Components
1. **LineChart** - Time series data
2. **BarChart** - Categorical comparisons
3. **DonutChart** - Proportional data
4. **AreaChart** - Trend visualization

---

## Phase 3: Layout Updates

### 3.1 Main Layout Structure
```
┌─────────────────────────────────────────────────────────┐
│  Header (60px)                                         │
├───────────┬────────────────────────────────────────────┤
│           │                                            │
│  Sidebar  │  Main Content Area                          │
│  (260px)  │                                            │
│           │  ┌─────────────────────────────────────┐    │
│           │  │ Page Title + Actions                │    │
│           │  ├─────────────────────────────────────┤    │
│           │  │ Content                            │    │
│           │  │                                     │    │
│           │  └─────────────────────────────────────┘    │
│           │                                            │
└───────────┴────────────────────────────────────────────┘
```

### 3.2 Dashboard Grid System
- **4-column grid** for stat cards
- **3-column grid** for chart cards
- **2-column grid** for mixed content
- **Responsive breakpoints**: xs (<576px), sm (≥576px), md (≥768px), lg (≥992px), xl (≥1200px), 2xl (≥1400px)

---

## Phase 4: Page Designs

### 4.1 Dashboard Main Page
```
┌─────────────────────────────────────────────────────┐
│  Welcome Header + Quick Actions                      │
├─────────────────────────────────────────────────────┤
│                                                     │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐    │
│  │ Total  │ │ Visits  │ │ Revenue │ │ Points │    │
│  │Customers│ │ This Mo │ │  This Mo│ │ Issued │    │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘    │
│                                                     │
├─────────────────────────────────────────────────────┤
│                                                     │
│  ┌─────────────────────┐ ┌─────────────────────┐     │
│  │ Visits Over Time    │ │ Points Activity    │     │
│  │ (Line Chart)        │ │ (Bar Chart)         │     │
│  │                     │ │                     │     │
│  └─────────────────────┘ └─────────────────────┘     │
│                                                     │
├─────────────────────────────────────────────────────┤
│                                                     │
│  ┌─────────────────────┐ ┌─────────────────────┐     │
│  │ Recent Customers    │ │ Recent Visits       │     │
│  │ + View All Link     │ │ + View All Link     │     │
│  │                     │ │                     │     │
│  │ ┌─────────────────┐ │ │ ┌─────────────────┐ │     │
│  │ │ List items...   │ │ │ │ List items...   │ │     │
│  │ └─────────────────┘ │ │ └─────────────────┘ │     │
│  └─────────────────────┘ └─────────────────────┘     │
│                                                     │
└─────────────────────────────────────────────────────┘
```

### 4.2 Customers Page
- Advanced search/filter panel
- Data table with columns: Name, Type, Contact, Nationality, Points, Visits, Status, Actions
- Bulk actions toolbar
- Export functionality

### 4.3 Visits Page
- Date range filter
- Outlet selector
- Data table with columns: ID, Customer, Outlet, Staff, Bill Amount, Points, Date, Actions

### 4.4 Wallets Page
- Customer wallet overview
- Points ledger table
- Rewards redemption section
- Points adjustment modal

### 4.5 Rewards Page
- Rewards catalog grid
- Create/edit reward form
- Redemption history

### 4.6 Campaigns Page
- Campaign list with status filters
- Performance metrics (opens, clicks, conversions)
- Create campaign wizard
- Campaign preview modal

### 4.7 Reports Page
- Overview dashboard
- Customer analytics
- Visit reports
- Loyalty program reports
- Exportable reports

### 4.8 Outlets Page
- Outlet management grid
- QR code management
- Social links configuration

### 4.9 Admin Page
- User management
- Role & permission management
- System settings
- Audit logs

---

## Phase 5: Visual Style Guide

### 5.1 Card Styling
```css
.card {
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-soft-md);
    border: 1px solid var(--gray-100);
    overflow: hidden;
    transition: all var(--transition-base);
}

.card:hover {
    box-shadow: var(--shadow-soft-lg);
    transform: translateY(-2px);
}
```

### 5.2 Button Styling
```css
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-2);
    padding: var(--spacing-3) var(--spacing-5);
    border-radius: var(--radius-lg);
    font-weight: var(--font-medium);
    font-size: var(--text-sm);
    transition: all var(--transition-fast);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
    color: white;
    border: none;
    box-shadow: var(--shadow-sm);
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-700), var(--primary-800));
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}
```

### 5.3 Table Styling
```css
.table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.table thead th {
    background: var(--gray-50);
    font-weight: var(--font-semibold);
    color: var(--text-secondary);
    font-size: var(--text-xs);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: var(--spacing-4);
    border-bottom: 1px solid var(--gray-200);
}

.table tbody td {
    padding: var(--spacing-4);
    border-bottom: 1px solid var(--gray-100);
    vertical-align: middle;
}

.table tbody tr:hover {
    background: var(--gray-50);
}
```

### 5.4 Form Styling
```css
.form-control,
.form-select {
    width: 100%;
    padding: var(--spacing-3) var(--spacing-4);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    font-size: var(--text-sm);
    background: white;
    transition: all var(--transition-fast);
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px var(--primary-100);
    outline: none;
}
```

---

## Phase 6: Charts & Analytics

### 6.1 Chart Colors
```javascript
const chartColors = {
    primary: '#6366f1',
    secondary: '#8b5cf6',
    success: '#10b981',
    warning: '#f59e0b',
    danger: '#ef4444',
    info: '#3b82f6',
    gradient: {
        purple: ['rgba(99, 102, 241, 0.3)', 'rgba(99, 102, 241, 0)'],
        blue: ['rgba(59, 130, 246, 0.3)', 'rgba(59, 130, 246, 0)'],
        green: ['rgba(16, 185, 129, 0.3)', 'rgba(16, 185, 129, 0)'],
    }
};
```

### 6.2 Chart Configuration
- Use Chart.js or ApexCharts
- Consistent styling across all charts
- Responsive design
- Tooltips with formatted data
- Smooth animations on load

---

## Phase 7: Deliverables Checklist

- [ ] **Design System CSS** - Comprehensive CSS file with all design tokens
- [ ] **Layout Component** - Updated app.blade.php with new structure
- [ ] **Sidebar Component** - Enhanced sidebar with animations
- [ ] **Header Component** - Modern header with notifications
- [ ] **Dashboard View** - Main dashboard page
- [ ] **Stat Card Component** - Reusable KPI cards
- [ ] **Data Card Component** - Generic content containers
- [ ] **Data Table Component** - Advanced table with filters
- [ ] **Chart Components** - Various chart wrappers
- [ ] **Customers View** - Customer management page
- [ ] **Visits View** - Visit management page
- [ ] **Wallets View** - Wallet overview page
- [ ] **Rewards View** - Rewards catalog page
- [ ] **Campaigns View** - Campaign management page
- [ ] **Reports View** - Analytics and reports page
- [ ] **Outlets View** - Outlet management page
- [ ] **Admin View** - Administration page
- [ ] **Empty States** - Stylized empty state components
- [ ] **Responsive Styles** - Mobile/tablet adaptations
- [ ] **Documentation** - Component usage guide

---

## File Structure
```
resources/
├── css/
│   ├── app.css              # Main stylesheet
│   ├── components/          # Component-specific styles
│   │   ├── _cards.css
│   │   ├── _sidebar.css
│   │   ├── _tables.css
│   │   ├── _forms.css
│   │   └── _charts.css
│   └── _variables.css       # Design tokens
├── views/
│   ├── components/
│   │   ├── sidebar.blade.php
│   │   ├── header.blade.php
│   │   ├── stat-card.blade.php
│   │   ├── data-card.blade.php
│   │   ├── chart-card.blade.php
│   │   ├── data-table.blade.php
│   │   ├── empty-state.blade.php
│   │   └── pagination.blade.php
│   ├── dashboard/
│   │   └── index.blade.php
│   ├── customers/
│   │   └── index.blade.php
│   ├── visits/
│   │   └── index.blade.php
│   ├── loyalty/
│   │   ├── wallets.blade.php
│   │   └── rewards.blade.php
│   ├── campaigns/
│   │   └── index.blade.php
│   ├── reports/
│   │   └── index.blade.php
│   ├── outlets/
│   │   └── index.blade.php
│   └── admin/
│       └── index.blade.php
└── layouts/
    └── app.blade.php
```

---

## Approval Required
Please review this plan and confirm:
1. Design direction aligns with your vision
2. Scope covers all required modules
3. Timeline expectations are reasonable
4. Any specific customizations needed

Once approved, I'll proceed with implementation.

