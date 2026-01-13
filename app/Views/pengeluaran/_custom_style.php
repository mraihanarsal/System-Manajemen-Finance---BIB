<style>
/* Import Google Font: Plus Jakarta Sans for a very modern, tech-forward look */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');

:root {
    --primary-color: #2563eb; /* Royal Blue */
    --primary-hover: #1d4ed8;
    --secondary-color: #64748b; /* Slate 500 */
    --success-color: #10b981; /* Emerald 500 */
    --warning-color: #f59e0b; /* Amber 500 */
    --danger-color: #ef4444; /* Red 500 */
    --light-bg: #f8fafc; /* Slate 50 */
    --card-bg: #ffffff;
    --text-main: #0f172a; /* Slate 900 */
    --text-muted: #64748b;
    --border-color: #e2e8f0; /* Slate 200 */
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}

body {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    background-color: var(--light-bg);
    color: var(--text-main);
}

/* Card Styling */
.card-premium {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
}

.card-premium:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.card-header-premium {
    background: transparent;
    border-bottom: 1px solid var(--border-color);
    padding: 1.5rem;
}

.card-body-premium {
    padding: 1.5rem;
}

/* Titles */
.page-title {
    font-weight: 700;
    color: var(--text-main);
    letter-spacing: -0.025em;
    font-size: 1.75rem;
}

.page-subtitle {
    color: var(--text-muted);
    font-size: 0.95rem;
}

/* Form Controls */
.form-control-premium {
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.2s;
    background-color: #f8fafc;
    /* Fix for dropdown text cutoff */
    line-height: 1.5;
    min-height: 48px; /* Ensure sufficient height */
}

select.form-control-premium {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 16px 12px;
    appearance: none;
    -webkit-appearance: none;
    padding-right: 2.5rem; /* Space for arrow */
}

.form-control-premium:focus {
    border-color: var(--primary-color);
    background-color: #fff;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    outline: none;
}

.form-label-premium {
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--text-main);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Buttons */
.btn-premium {
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary-premium {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
    border: none;
    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
}

.btn-primary-premium:hover {
    filter: brightness(110%);
    transform: translateY(-1px);
    box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
    color: white;
}

/* Table */
.table-premium-container {
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--border-color);
}

.table-premium {
    width: 100%;
    border-collapse: collapse;
}

.table-premium thead th {
    background-color: #f1f5f9;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    padding: 1rem 1.5rem;
    letter-spacing: 0.05em;
    border-bottom: 1px solid var(--border-color);
}

.table-premium tbody td {
    padding: 1rem 1.5rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-main);
    font-size: 0.95rem;
}

.table-premium tbody tr:last-child td {
    border-bottom: none;
}

.table-premium tbody tr:hover {
    background-color: #f8fafc;
}

/* Badges */
.badge-premium {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 0.75rem;
}
.badge-soft-primary {
    background-color: rgba(37, 99, 235, 0.1);
    color: var(--primary-color);
}
.badge-soft-secondary {
    background-color: rgba(100, 116, 139, 0.1);
    color: var(--secondary-color);
}

/* Total Widget */
.total-widget {
    background: linear-gradient(135deg, #1e293b, #0f172a);
    color: white;
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: var(--shadow-lg);
}
.total-label {
    opacity: 0.8;
    font-size: 0.9rem;
    font-weight: 500;
}
.total-amount {
    font-size: 1.75rem;
    font-weight: 700;
    background: -webkit-linear-gradient(eee, #fff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}
</style>