<?php
/**
 * Reusable Modal System for CMS
 * Based on the design from records.php prescription success modal
 */

// Function to show success modal
function showSuccessModal($message, $title = 'Success', $autoClose = false, $redirect = null) {
    $modalId = 'successModal_' . uniqid();
    $redirectScript = $redirect ? "if (redirect) { window.location.href = '$redirect'; }" : '';
    
    echo "
    <div id='$modalId' style='position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);'>
        <div style='background:rgba(255,255,255,0.95); color:#2563eb; min-width:300px; max-width:90vw; padding:24px 32px; border-radius:16px; box-shadow:0 4px 32px rgba(37,99,235,0.15); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #2563eb; display:flex; flex-direction:column; gap:16px; pointer-events:auto;'>
            <div style='display:flex; align-items:center; justify-content:center; gap:12px;'>
                <span style='font-size:2rem;line-height:1;color:#2563eb;'>&#10003;</span>
                <span style='color:#374151;'>$message</span>
            </div>
            <div style='display:flex; gap:12px; justify-content:center;'>
                <button id='okBtn' style='background:#2563eb; color:white; padding:8px 16px; border-radius:8px; font-weight:500; border:none; cursor:pointer;'>Okay</button>
                <button id='cancelBtn' style='background:#f3f4f6; color:#374151; padding:8px 16px; border-radius:8px; font-weight:500; border:1px solid #d1d5db; cursor:pointer;'>Cancel</button>
            </div>
        </div>
    </div>
    <script>
        var modal = document.getElementById('$modalId');
        var okBtn = modal.querySelector('#okBtn');
        var cancelBtn = modal.querySelector('#cancelBtn');
        var redirect = '$redirect';
        
        okBtn.onclick = function() {
            modal.style.transition = 'opacity 0.3s';
            modal.style.opacity = '0';
            setTimeout(function() { 
                if (modal && modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
                $redirectScript
            }, 300);
        };
        
        cancelBtn.onclick = function() {
            modal.style.transition = 'opacity 0.3s';
            modal.style.opacity = '0';
            setTimeout(function() { 
                if (modal && modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
            }, 300);
        };
    </script>";
}

// Function to show error modal
function showErrorModal($message, $title = 'Error', $autoClose = false) {
    $modalId = 'errorModal_' . uniqid();
    
    echo "
    <div id='$modalId' style='position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);'>
        <div style='background:rgba(255,255,255,0.95); color:#dc2626; min-width:300px; max-width:90vw; padding:24px 32px; border-radius:16px; box-shadow:0 4px 32px rgba(220,38,38,0.15); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #dc2626; display:flex; flex-direction:column; gap:16px; pointer-events:auto;'>
            <div style='display:flex; align-items:center; justify-content:center; gap:12px;'>
                <span style='font-size:2rem;line-height:1;color:#dc2626;'>&#9888;</span>
                <span style='color:#374151;'>$message</span>
            </div>
            <div style='display:flex; gap:12px; justify-content:center;'>
                <button id='okBtn' style='background:#dc2626; color:white; padding:8px 16px; border-radius:8px; font-weight:500; border:none; cursor:pointer;'>Okay</button>
                <button id='cancelBtn' style='background:#f3f4f6; color:#374151; padding:8px 16px; border-radius:8px; font-weight:500; border:1px solid #d1d5db; cursor:pointer;'>Cancel</button>
            </div>
        </div>
    </div>
    <script>
        var modal = document.getElementById('$modalId');
        var okBtn = modal.querySelector('#okBtn');
        var cancelBtn = modal.querySelector('#cancelBtn');
        
        okBtn.onclick = function() {
            modal.style.transition = 'opacity 0.3s';
            modal.style.opacity = '0';
            setTimeout(function() { 
                if (modal && modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
            }, 300);
        };
        
        cancelBtn.onclick = function() {
            modal.style.transition = 'opacity 0.3s';
            modal.style.opacity = '0';
            setTimeout(function() { 
                if (modal && modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
            }, 300);
        };
    </script>";
}

// Function to show warning modal
function showWarningModal($message, $title = 'Warning', $autoClose = false) {
    $modalId = 'warningModal_' . uniqid();
    
    echo "
    <div id='$modalId' style='position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);'>
        <div style='background:rgba(255,255,255,0.95); color:#d97706; min-width:300px; max-width:90vw; padding:24px 32px; border-radius:16px; box-shadow:0 4px 32px rgba(217,119,6,0.15); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #d97706; display:flex; flex-direction:column; gap:16px; pointer-events:auto;'>
            <div style='display:flex; align-items:center; justify-content:center; gap:12px;'>
                <span style='font-size:2rem;line-height:1;color:#d97706;'>&#9888;</span>
                <span style='color:#374151;'>$message</span>
            </div>
            <div style='display:flex; gap:12px; justify-content:center;'>
                <button id='okBtn' style='background:#d97706; color:white; padding:8px 16px; border-radius:8px; font-weight:500; border:none; cursor:pointer;'>Okay</button>
                <button id='cancelBtn' style='background:#f3f4f6; color:#374151; padding:8px 16px; border-radius:8px; font-weight:500; border:1px solid #d1d5db; cursor:pointer;'>Cancel</button>
            </div>
        </div>
    </div>
    <script>
        var modal = document.getElementById('$modalId');
        var okBtn = modal.querySelector('#okBtn');
        var cancelBtn = modal.querySelector('#cancelBtn');
        
        okBtn.onclick = function() {
            modal.style.transition = 'opacity 0.3s';
            modal.style.opacity = '0';
            setTimeout(function() { 
                if (modal && modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
            }, 300);
        };
        
        cancelBtn.onclick = function() {
            modal.style.transition = 'opacity 0.3s';
            modal.style.opacity = '0';
            setTimeout(function() { 
                if (modal && modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
            }, 300);
        };
    </script>";
}

// Function to show info modal
function showInfoModal($message, $title = 'Information', $autoClose = false) {
    $modalId = 'infoModal_' . uniqid();
    
    echo "
    <div id='$modalId' style='position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);'>
        <div style='background:rgba(255,255,255,0.95); color:#059669; min-width:300px; max-width:90vw; padding:24px 32px; border-radius:16px; box-shadow:0 4px 32px rgba(5,150,105,0.15); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #059669; display:flex; flex-direction:column; gap:16px; pointer-events:auto;'>
            <div style='display:flex; align-items:center; justify-content:center; gap:12px;'>
                <span style='font-size:2rem;line-height:1;color:#059669;'>&#8505;</span>
                <span style='color:#374151;'>$message</span>
            </div>
            <div style='display:flex; gap:12px; justify-content:center;'>
                <button id='okBtn' style='background:#059669; color:white; padding:8px 16px; border-radius:8px; font-weight:500; border:none; cursor:pointer;'>Okay</button>
                <button id='cancelBtn' style='background:#f3f4f6; color:#374151; padding:8px 16px; border-radius:8px; font-weight:500; border:1px solid #d1d5db; cursor:pointer;'>Cancel</button>
            </div>
        </div>
    </div>
    <script>
        var modal = document.getElementById('$modalId');
        var okBtn = modal.querySelector('#okBtn');
        var cancelBtn = modal.querySelector('#cancelBtn');
        
        okBtn.onclick = function() {
            modal.style.transition = 'opacity 0.3s';
            modal.style.opacity = '0';
            setTimeout(function() { 
                if (modal && modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
            }, 300);
        };
        
        cancelBtn.onclick = function() {
            modal.style.transition = 'opacity 0.3s';
            modal.style.opacity = '0';
            setTimeout(() => { 
                if (modal && modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
            }, 300);
        };
    </script>";
}

// JavaScript function to show modals dynamically
function getModalJavaScript() {
    return <<<'JS'
    <script>
    // Global modal functions
    function showModal(type, message, title = '', autoClose = false, redirect = null) {
        const modalId = type + 'Modal_' + Date.now();
        const colors = {
            success: { color: '#2563eb', icon: '&#10003;' },
            error: { color: '#dc2626', icon: '&#9888;' },
            warning: { color: '#d97706', icon: '&#9888;' },
            info: { color: '#059669', icon: '&#8505;' }
        };
        
        const config = colors[type] || colors.info;
        
        const modal = document.createElement('div');
        modal.id = modalId;
        modal.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);';
        
        modal.innerHTML = `
            <div style='background:rgba(255,255,255,0.95); color:${config.color}; min-width:300px; max-width:90vw; padding:24px 32px; border-radius:16px; box-shadow:0 4px 32px ${config.color}20; font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid ${config.color}; display:flex; flex-direction:column; gap:16px; pointer-events:auto;'>
                <div style='display:flex; align-items:center; justify-content:center; gap:12px;'>
                    <span style='font-size:2rem;line-height:1;color:${config.color};'>${config.icon}</span>
                    <span style='color:#374151;'>${message}</span>
                </div>
                <div style='display:flex; gap:12px; justify-content:center;'>
                    <button id='okBtn' style='background:${config.color}; color:white; padding:8px 16px; border-radius:8px; font-weight:500; border:none; cursor:pointer;'>Okay</button>
                    <button id='cancelBtn' style='background:#f3f4f6; color:#374151; padding:8px 16px; border-radius:8px; font-weight:500; border:1px solid #d1d5db; cursor:pointer;'>Cancel</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        const okBtn = modal.querySelector('#okBtn');
        const cancelBtn = modal.querySelector('#cancelBtn');
        
        okBtn.onclick = function() {
            modal.style.transition = 'opacity 0.3s';
            modal.style.opacity = '0';
            setTimeout(() => { 
                if (modal && modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
                if (redirect) {
                    window.location.href = redirect;
                }
            }, 300);
        };
        
        cancelBtn.onclick = function() {
            modal.style.transition = 'opacity 0.3s';
            modal.style.opacity = '0';
            setTimeout(() => { 
                if (modal && modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
            }, 300);
        };
        
        return modalId;
    }
    
    // Convenience functions
    function showSuccessModal(message, title = 'Success', autoClose = false, redirect = null) {
        return showModal('success', message, title, autoClose, redirect);
    }
    
    function showErrorModal(message, title = 'Error', autoClose = false) {
        return showModal('error', message, title, autoClose);
    }
    
    function showWarningModal(message, title = 'Warning', autoClose = false) {
        return showModal('warning', message, title, autoClose);
    }
    
    function showInfoModal(message, title = 'Information', autoClose = false) {
        return showModal('info', message, title, autoClose);
    }
    
    // Replace all alert() calls with modal
    function replaceAlerts() {
        // Override the global alert function
        window.originalAlert = window.alert;
        window.alert = function(message) {
            showErrorModal(message, 'Alert');
        };
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', replaceAlerts);
    } else {
        replaceAlerts();
    }
    </script>
JS;
}

// Function to include the modal system
function includeModalSystem() {
    echo getModalJavaScript();
}
?>
