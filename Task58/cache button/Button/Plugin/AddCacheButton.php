<?php

// namespace Tychons\Button\Plugin;

// use Magento\Backend\Block\Widget\Context;
// use Magento\Framework\View\Element\AbstractBlock;

// class AddCacheButton
// {
//     /**
//      * @var Context
//      */
//     private Context $context;

//     /**
//      * AddCacheButton constructor.
//      *
//      * @param Context $context
//      */
//     public function __construct(
//         Context $context
//     ) {
//         $this->context = $context;
//     }

//     /**
//      * Add a custom button to the Cache Management page
//      *
//      * @param AbstractBlock $subject
//      * @param \Closure $proceed
//      * @return string
//      */
//     public function aroundToHtml(AbstractBlock $subject, \Closure $proceed)
//     {
//         // Call the original method to get existing content
//         $html = $proceed();

//         // JavaScript to create and insert the button below the "Flush Magento Cache" button
//         $html .= '
//             <script>
//                 document.addEventListener("DOMContentLoaded", function() {
//                     // Find the "Flush Magento Cache" button by its ID
//                     const flushCacheButton = document.getElementById("flush_magento");

//                     // Only proceed if the button exists
//                     if (flushCacheButton) {
//                         // Create the custom button element
//                         const customButton = document.createElement("button");
//                         customButton.id = "custom-cache-button";
//                         customButton.type = "button";
//                         customButton.className = "action-default scalable action-secondary";
//                         customButton.innerHTML = "<span>Custom Cache Action</span>";

//                         // Define the click behavior for the custom button
//                         customButton.onclick = function() {
//                             alert("Custom Cache Action button clicked!");
//                             // Add any additional custom functionality here
//                         };

//                         // Insert the custom button right after the "Flush Magento Cache" button
//                         flushCacheButton.parentNode.insertBefore(customButton, flushCacheButton.nextSibling);
//                     }
//                 });
//             </script>
//         ';

//         return $html;
//     }
// }
 

namespace Tychons\Button\Plugin;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\AbstractBlock;

class AddCacheButton
{
    /**
     * @var Context
     */
    private Context $context;

    /**
     * AddCacheButton constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * Add a custom button to the Cache Management page
     *
     * @param AbstractBlock $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundToHtml(AbstractBlock $subject, \Closure $proceed)
    {
        // Call the original method to get existing content
        $html = $proceed();

        // JavaScript to create and insert the button below the "Flush Magento Cache" button
        $html .= '
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // Find the "Flush Magento Cache" button by its ID
                    const flushCacheButton = document.getElementById("flush_magento");

                    // Only proceed if the button exists
                    if (flushCacheButton) {
                        // Create the custom button element
                        const customButton = document.createElement("button");
                        customButton.id = "custom-cache-button";
                        customButton.type = "button";
                        customButton.className = "action-default scalable action-secondary";
                        customButton.innerHTML = "<span>Custom Cache Action</span>";

                        // Define the click behavior to trigger the same action as the Flush Magento Cache button
                        customButton.onclick = function() {
                            // Trigger the "Flush Magento Cache" button programmatically
                            flushCacheButton.click();
                        };

                        // Insert the custom button right after the "Flush Magento Cache" button
                        flushCacheButton.parentNode.insertBefore(customButton, flushCacheButton.nextSibling);
                    }
                });
            </script>
        ';

        return $html;
    }
}

