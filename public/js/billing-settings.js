window.BILLING_SETTINGS = {
    taxEnabled: false,
    taxPercentage: 0,
    serviceChargeEnabled: false,
    serviceChargePercentage: 0,
    deliveryCharge: 0,
    currency: 'NPR'
};

window.loadBillingSettings = async function () {
    try {
        const res = await fetch('/api/v1/staff/settings', {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`
            }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            showToast(data.message || 'Failed to load settings', 'error');
            return false;
        }

        Object.assign(window.BILLING_SETTINGS, data.data);

        console.log('Billing settings:', window.BILLING_SETTINGS);

        return true;

    } catch (error) {
        console.error('Billing settings error:', error);
        return false;
    }
};