/**
 * Custom Confirmation Modal & Success/Error Toast Notifications
 * SDIT Al Fatah Admin Panel
 */

(function() {
    // Inject CSS styles for modal and toasts dynamically
    const style = document.createElement('style');
    style.innerHTML = `
        /* Custom Confirm Modal Styling */
        #customConfirmModal .modal-content {
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: none;
        }
        #customConfirmModal .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            border-radius: 10px;
            padding: 10px 24px;
            font-weight: bold;
        }
        #customConfirmModal .btn-danger:hover {
            background-color: #bd2130;
            border-color: #b21f2d;
        }
        #customConfirmModal .btn-light {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #6c757d;
            border-radius: 10px;
            padding: 10px 24px;
            font-weight: 600;
        }
        #customConfirmModal .btn-light:hover {
            background-color: #e2e6ea;
            color: #495057;
        }
        
        /* Toast Notification Styling */
        #customToastContainer {
            position: fixed;
            top: 25px;
            right: 25px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }
        .custom-toast {
            min-width: 300px;
            max-width: 400px;
            background-color: #1acc8d; /* Theme Green for Success */
            color: #ffffff;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            pointer-events: auto;
        }
        .custom-toast.toast-error {
            background-color: #dc3545 !important; /* Theme Red for Error */
        }
        .custom-toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        .custom-toast-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .custom-toast-close {
            background: none;
            border: none;
            color: rgba(255,255,255,0.8);
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            transition: color 0.2s;
        }
        .custom-toast-close:hover {
            color: #ffffff;
        }
    `;
    document.head.appendChild(style);

    // Document Ready Processing
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Process delete confirmation forms
        const forms = document.querySelectorAll("form");
        forms.forEach(form => {
            const onsubmitAttr = form.getAttribute("onsubmit");
            if (onsubmitAttr && onsubmitAttr.includes("confirm(")) {
                // Remove the inline handler to prevent synchronous prompt
                form.removeAttribute("onsubmit");
                
                // Extract the message
                let msg = "Apakah Anda yakin ingin menghapus data ini?";
                const match = onsubmitAttr.match(/confirm\(['"](.+?)['"]\)/);
                if (match && match[1]) {
                    msg = match[1];
                }
                
                // Intercept submit event
                form.addEventListener("submit", function(e) {
                    e.preventDefault();
                    showCustomConfirmModal(msg, function() {
                        form.submit();
                    });
                });
            }
        });

        // 2. Convert standard bootstrap success alerts to elegant toasts automatically
        const successAlerts = document.querySelectorAll(".alert-success");
        successAlerts.forEach(alert => {
            alert.style.display = "none";
            const temp = alert.cloneNode(true);
            const closeBtn = temp.querySelector(".btn-close");
            if (closeBtn) closeBtn.remove();
            const msg = temp.textContent.trim();
            showSuccessToast(msg);
        });

        // 3. Convert standard bootstrap danger/error alerts to error toasts automatically
        const dangerAlerts = document.querySelectorAll(".alert-danger");
        dangerAlerts.forEach(alert => {
            alert.style.display = "none";
            const temp = alert.cloneNode(true);
            const closeBtn = temp.querySelector(".btn-close");
            if (closeBtn) closeBtn.remove();
            const msg = temp.textContent.trim();
            showErrorToast(msg);
        });
    });

    // 4. Intercept clicks on links with confirm dialogs in capture phase (like Logout button)
    document.addEventListener("click", function(e) {
        const anchor = e.target.closest("a");
        if (anchor) {
            const onclickAttr = anchor.getAttribute("onclick");
            if (onclickAttr && onclickAttr.includes("confirm(")) {
                e.preventDefault();
                e.stopPropagation();
                
                let msg = "Apakah Anda yakin?";
                const match = onclickAttr.match(/confirm\(['"](.+?)['"]\)/);
                if (match && match[1]) {
                    msg = match[1];
                }
                
                const isDelete = msg.toLowerCase().includes("hapus") || msg.toLowerCase().includes("delete");
                const title = isDelete ? "Hapus Data?" : "Konfirmasi";
                const btnText = isDelete ? "Ya, Hapus" : "Ya, Lanjutkan";
                const iconClass = isDelete ? "fa-solid fa-triangle-exclamation text-danger" : "fa-solid fa-circle-question text-primary";
                
                showCustomConfirmModal(msg, function() {
                    window.location.href = anchor.href;
                }, btnText, title, iconClass);
            }
        }
    }, true); // Use capture phase so we run before the inline browser click bubbles

    // Custom Modal Display Function
    function showCustomConfirmModal(message, onConfirm, btnText = "Ya, Hapus", title = "Hapus Data?", iconClass = "fa-solid fa-triangle-exclamation text-danger") {
        let modalEl = document.getElementById("customConfirmModal");
        if (modalEl) {
            modalEl.remove();
        }
        
        const isDelete = btnText.includes("Hapus");
        
        const modalHTML = `
        <div class="modal fade" id="customConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
                <div class="modal-content">
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3">
                            <i class="${iconClass} fa-3x" ${!isDelete ? 'style="color: #1acc8d;"' : ''}></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">${title}</h5>
                        <p class="text-secondary small mb-4">${message}</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="${isDelete ? 'btn btn-danger' : 'btn btn-success text-white'}" id="btnConfirmDelete" style="${!isDelete ? 'background-color: #1acc8d; border-color: #1acc8d;' : ''}">${btnText}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        modalEl = document.getElementById("customConfirmModal");
        
        if (typeof bootstrap !== 'undefined') {
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
            
            document.getElementById("btnConfirmDelete").onclick = function() {
                bsModal.hide();
                onConfirm();
            };
        } else {
            // Fallback for safety
            if (confirm(message)) {
                onConfirm();
            }
        }
    }

    // Generic Toast Display Function
    function showToast(message, type = "success") {
        let container = document.getElementById("customToastContainer");
        if (!container) {
            container = document.createElement("div");
            container.id = "customToastContainer";
            document.body.appendChild(container);
        }
        
        const isError = (type === "error");
        const bgClass = isError ? "toast-error" : "";
        const iconClass = isError ? "fa-solid fa-circle-xmark" : "fa-solid fa-circle-check";
        
        const toastHTML = `
        <div class="custom-toast ${bgClass}">
            <div class="custom-toast-content">
                <i class="${iconClass} fs-5"></i>
                <span class="fw-semibold small">${message}</span>
            </div>
            <button class="custom-toast-close">&times;</button>
        </div>`;
        
        container.insertAdjacentHTML('beforeend', toastHTML);
        const toastItem = container.lastElementChild;
        
        // Slide-in animation
        setTimeout(() => {
            toastItem.classList.add("show");
        }, 10);
        
        const closeToast = () => {
            toastItem.classList.remove("show");
            setTimeout(() => {
                toastItem.remove();
            }, 400);
        };
        
        toastItem.querySelector(".custom-toast-close").onclick = closeToast;
        setTimeout(closeToast, 4000);
    }

    function showSuccessToast(message) {
        showToast(message, "success");
    }

    function showErrorToast(message) {
        showToast(message, "error");
    }

    window.showSuccessToast = showSuccessToast;
    window.showErrorToast = showErrorToast;
})();
