/**
 * IP Address Lookup — Find your IP and get geolocation data.
 * Uses ipify (your IP) and ipapi.co (details), both free without a key.
 */
(function () {
    'use strict';
    if (!document.getElementById('ip-input')) return;

    var input = document.getElementById('ip-input');
    var output = document.getElementById('ip-output');
    var resultPanel = document.getElementById('ip-result');
    var statusEl = document.getElementById('ip-status');
    var lookupBtn = document.getElementById('ip-lookup');

    async function lookup() {
        var ip = input.value.trim();
        statusEl.textContent = 'Looking up...';
        resultPanel.style.display = '';
        output.innerHTML = '<p style="color:#6b7280">Fetching data...</p>';

        try {
            var target = ip;
            if (!target) {
                var ipRes = await fetch('https://api.ipify.org?format=json');
                if (!ipRes.ok) throw new Error('Could not fetch your IP');
                var ipData = await ipRes.json();
                target = ipData.ip;
                input.value = target;
            }

            var data;
            var apiRes = await fetch('https://ipapi.co/' + encodeURIComponent(target) + '/json/');
            if (apiRes.ok) {
                data = await apiRes.json();
            } else {
                var fallbackRes = await fetch('https://ipwho.is/' + encodeURIComponent(target));
                var fb = await fallbackRes.json();
                data = fb.success ? {
                    ip: fb.ip,
                    country_name: fb.country,
                    country_code: fb.country_code,
                    region: fb.region,
                    city: fb.city,
                    postal: fb.postal,
                    latitude: fb.latitude,
                    longitude: fb.longitude,
                    timezone: fb.timezone && fb.timezone.id,
                    utc_offset: fb.timezone && fb.timezone.utc ? (fb.timezone.utc / 3600) : null,
                    org: fb.connection && fb.connection.isp,
                    asn: fb.connection && fb.connection.asn,
                    currency: fb.currency && fb.currency.code,
                    currency_name: fb.currency && fb.currency.name,
                    country_calling_code: fb.connection && fb.connection.calling_code
                } : null;
                if (!data) throw new Error('Lookup failed');
            }

            if (data.error) {
                statusEl.textContent = 'Error';
                output.innerHTML = '<p style="color:#dc2626">' + (data.reason || 'Invalid IP address. Please check and try again.') + '</p>';
                return;
            }

            var rows = [
                ['IP Address', data.ip || target],
                ['Country', [data.country_name, data.country_code].filter(Boolean).join(' (') + (data.country_code ? ')' : '')],
                ['Region / State', data.region || '-'],
                ['City', data.city || '-'],
                ['Postal Code', data.postal || '-'],
                ['Latitude', data.latitude != null ? data.latitude : '-'],
                ['Longitude', data.longitude != null ? data.longitude : '-'],
                ['Timezone', data.timezone || '-'],
                ['UTC Offset', (data.utc_offset != null ? data.utc_offset + ' hours' : '-')],
                ['ISP', data.org || '-'],
                ['ASN', data.asn ? 'AS' + data.asn : '-'],
                ['Currency', data.currency ? data.currency + (data.currency_name ? ' (' + data.currency_name + ')' : '') : '-'],
                ['Calling Code', data.country_calling_code ? '+' + data.country_calling_code : '-'],
                ['Continent', data.continent_code || '-'],
                ['Languages', data.languages || '-']
            ];

            var html = '<div style="border:1px solid rgba(128,128,128,0.2);border-radius:8px;overflow:hidden">';
            rows.forEach(function (r, i) {
                html += '<div style="display:flex;justify-content:space-between;padding:10px 16px;' +
                    (i % 2 === 0 ? 'background:#f9fafb' : 'background:#fff') + '" >' +
                    '<span style="color:#6b7280;font-size:14px">' + r[0] + '</span>' +
                    '<span style="font-weight:600;font-size:14px;color:#111827;text-align:right">' + r[1] + '</span>' +
                    '</div>';
            });
            html += '</div>';

            output.innerHTML = html;
            statusEl.textContent = data.ip || target;
            TCTP.toast('IP lookup complete!', '\u2705');
        } catch (e) {
            statusEl.textContent = 'Error';
            output.innerHTML = '<p style="color:#dc2626">Lookup failed. Please check your connection or the IP address and try again.</p>';
        }
    }

    lookupBtn.addEventListener('click', lookup);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') lookup();
    });

    lookup();
})();
