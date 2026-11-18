// Youth Policy Tracker Core Application Scripts

// Global event listener for Cookie Consent & Google Analytics 4
window.addEventListener('ga4-consent-granted', (e) => {
    const gaId = window.GA_MEASUREMENT_ID || 'G-XXXXXXXXXX';
    if (!window.gtagLoaded && gaId && gaId !== 'G-XXXXXXXXXX') {
        const script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtag/js?id=${gaId}`;
        document.head.appendChild(script);

        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        window.gtag = gtag;
        gtag('js', new Date());
        gtag('config', gaId, { 'anonymize_ip': true });
        window.gtagLoaded = true;
    }
});

// Helper for dispatching custom GA events
window.trackTrackerEvent = (eventName, eventParams = {}) => {
    if (window.gtag && window.gtagLoaded) {
        window.gtag('event', eventName, eventParams);
    }
};

// Lazy-loaded Chart.js loader for Analytics Dashboard
window.loadTrackerCharts = async (chartConfigs) => {
    try {
        const { Chart, registerables } = await import('chart.js');
        Chart.register(...registerables);

        chartConfigs.forEach(cfg => {
            const canvas = document.getElementById(cfg.canvasId);
            if (!canvas) return;
            if (canvas._chartInstance) {
                canvas._chartInstance.destroy();
            }
            canvas._chartInstance = new Chart(canvas, cfg.options);
        });
    } catch (err) {
        console.error('Failed to load Chart.js:', err);
    }
};

// Lazy-loaded PDF.js viewer
window.loadPdfViewer = async (containerId, pdfUrl) => {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = '<div class="p-8 text-center text-ink-600"><i class="fas fa-circle-notch fa-spin mr-2"></i> Loading certified policy document...</div>';

    try {
        const pdfjsLib = await import('pdfjs-dist/build/pdf.mjs');
        pdfjsLib.GlobalWorkerOptions.workerSrc = '/vendor/pdfjs/pdf.worker.mjs';

        const loadingTask = pdfjsLib.getDocument(pdfUrl);
        const pdf = await loadingTask.promise;
        container.innerHTML = '';

        for (let pageNum = 1; pageNum <= Math.min(pdf.numPages, 10); pageNum++) {
            const page = await pdf.getPage(pageNum);
            const scale = 1.2;
            const viewport = page.getViewport({ scale });

            const canvas = document.createElement('canvas');
            canvas.className = 'w-full mb-4 shadow-sm border border-ink-200 rounded';
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            container.appendChild(canvas);
            await page.render({ canvasContext: context, viewport }).promise;
        }

        if (pdf.numPages > 10) {
            const notice = document.createElement('div');
            notice.className = 'p-4 text-center text-xs text-ink-500 bg-ink-50 border border-ink-200 rounded';
            notice.textContent = `Showing first 10 of ${pdf.numPages} pages. Download full Certified True Copy to read all pages.`;
            container.appendChild(notice);
        }
    } catch (err) {
        container.innerHTML = '<div class="p-8 text-center text-rose-700 bg-rose-50 border border-rose-200 rounded"><i class="fas fa-triangle-exclamation mr-2"></i> Unable to preview document directly. Please use the download link.</div>';
        console.error('PDF preview error:', err);
    }
};
