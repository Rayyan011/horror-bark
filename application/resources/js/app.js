import './bootstrap';
import QRCode from 'qrcode';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-qr-generator]').forEach((root) => {
        const trigger = root.querySelector('[data-qr-button]');
        const output = root.querySelector('[data-qr-output]');
        const status = root.querySelector('[data-qr-status]');
        const download = root.querySelector('[data-qr-download]');
        const payload = root.dataset.qrPayload;

        if (!trigger || !output || !status || !payload) {
            return;
        }

        let rendered = false;

        trigger.addEventListener('click', async () => {
            if (rendered) {
                output.classList.toggle('hidden');
                status.classList.toggle('hidden');
                return;
            }

            trigger.setAttribute('disabled', 'disabled');
            status.textContent = 'Generating QR code...';
            status.classList.remove('hidden');

            try {
                const svg = await QRCode.toString(payload, {
                    type: 'svg',
                    margin: 1,
                    width: 220,
                    color: {
                        dark: '#f5f2f8',
                        light: '#0d0a12',
                    },
                });

                output.innerHTML = svg;
                output.classList.remove('hidden');
                status.textContent = 'QR ready for scan or download.';
                rendered = true;

                if (download) {
                    download.classList.remove('hidden');
                    download.classList.add('theme-qr-download');
                    download.setAttribute('href', `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}`);
                    download.textContent = 'Download QR';
                }
            } catch (error) {
                status.textContent = 'QR generation failed. Try again.';
                console.error(error);
            } finally {
                trigger.removeAttribute('disabled');
            }
        });
    });
});
