<?php
// Heading
$_['heading_title']				= '設定';

// Text
$_['text_success']				= '成功：您已經修改設定！';
$_['text_image_manager']		= '圖片管理';
$_['text_browse']				= '瀏覽檔案';
$_['text_clear']				= '清除圖片';
$_['text_shipping']				= '寄送地址';
$_['text_payment']				= '付款地址';
$_['text_mail']					= '郵件';
$_['text_smtp']					= 'SMTP';

// Entry
$_['entry_name']				= '商店名稱：';
$_['entry_owner']				= '商店店長：';
$_['entry_address']				= '地址：';
$_['entry_email']				= '電子郵件：';
$_['entry_telephone']			= '手機：';
$_['entry_fax']					= '傳真：';
$_['entry_title']				= '標題：';
$_['entry_meta_description']	= 'Meta 標籤描述：';
$_['entry_layout']				= '預設版面：';
$_['entry_template']			= '版型：';
$_['entry_country']				= '國家：';
$_['entry_zone']				= '地區 / 區域：';
$_['entry_language']			= '語系：';
$_['entry_admin_language']		= '管理語言：';
$_['entry_currency']			= '貨幣：<br /><span class="help">變更預設貨幣。Clear your browser cache to see the change and reset your existing cookie.</span>';
$_['entry_currency_auto']		= '自動更新貨幣：<br /><span class="help">Set your store to automatically update currencies daily.</span>';
$_['entry_length_class']		= '長度類別：';
$_['entry_weight_class']		= '重量類別：';
$_['entry_catalog_limit']		= '每頁項目的預設值 (前台)：<br /><span class="help">Determines how many catalog items are shown per page (產品、類別、等)</span>';
$_['entry_admin_limit']			= '每頁項目的預設值 (後台)：<br /><span class="help">Determines how many admin items are shown per page (訂單、客戶、等)</span>';
$_['entry_tax']					= 'Display Prices With Tax:';
$_['entry_tax_default']			= 'Use Store Tax Address:<br /><span class="help">Use the store address to calculate taxes if no one is logged in. You can choose to use the store address for the customers shipping or payment address.</span>';
$_['entry_tax_customer']		= 'Use Customer Tax Address:<br /><span class="help">Use the customers default address when they login to calculate taxes. You can choose to use the default address for the customers shipping or payment address.</span>';
$_['entry_invoice']				= '發票起始編號：<br /><span class="help">Set the starting number the invoices will begin from.</span>';
$_['entry_invoice_prefix']		= 'Invoice Prefix:<br /><span class="help">Set the invoice prefix (e.g. INV-2011-00). Invoice ID\'s will start at 1 for each unique prefix</span>';
$_['entry_customer_group']		= '客戶群組：<br /><span class="help">預設客戶群組。</span>';
$_['entry_customer_price']		= '登入顯示價格：<br /><span class="help">Only show prices when a customer is logged in.</span>';
$_['entry_customer_approval']	= 'Approve New Customers:<br /><span class="help">Don\'t allow new customer to login until their account has been approved.</span>';
$_['entry_guest_checkout']		= '訪客結帳：<br /><span class="help">Allow customers to checkout without creating an account. This will not be available when a downloadable product is in the shopping cart.</span>';
$_['entry_account']				= '帳戶條款：<br /><span class="help">Forces people to agree to terms before an account can be created.</span>';
$_['entry_checkout']			= '結帳條款：<br /><span class="help">Forces people to agree to terms before an a customer can checkout.</span>';
$_['entry_affiliate']			= '加盟條款：<br /><span class="help">Forces people to agree to terms before an affiliate account can be created.</span>';
$_['entry_commission']			= '加盟佣金 (%)：<br /><span class="help">The default affiliate commission percentage.</span>';
$_['entry_stock_display']		= '顯示庫存：<br /><span class="help">Display stock quantity on the product page.</span>';
$_['entry_stock_warning']		= 'Show Out Of Stock Warning:<br /><span class="help">Display out of stock message on the shopping cart page if a product is out of stock but stock checkout is yes. (Warning always shows if stock checkout is no)</span>';
$_['entry_stock_checkout']		= 'Stock Checkout:<br /><span class="help">Allow customers to still checkout if the products they are ordering are not in stock.</span>';
$_['entry_stock_status']		= 'Out of Stock Status:<br /><span class="help">Set the default out of stock status selected in product edit.</span>';
$_['entry_order_status']		= '訂單狀態：<br /><span class="help">Set the default order status when an order is processed.</span>';
$_['entry_complete_status']		= '完成訂單的狀態：<br /><span class="help">Set the order status the customers order must reach before they are allowed to access their downloadable products and gift vouchers.</span>';
$_['entry_return_status']		= '退換貨狀態：<br /><span class="help">Set the default return status when an returns request is submitted.</span>';
$_['entry_review']				= '允許評論：<br /><span class="help">Enable/Disable new review entry and display of existing reviews</span>';
$_['entry_download']			= '允許下載：';
$_['entry_upload_allowed']		= 'Allowed Upload File Extensions:<br /><span class="help">Add which file extensions are allowed to be uploaded. 使用逗號分隔數值。</span>';
$_['entry_cart_weight']			= 'Display Weight on Cart Page:<br /><span class="help">Show the cart weight on the cart page</span>';
$_['entry_logo']				= '商店商標：';
$_['entry_icon']				= '圖示：<br /><span class="help">The icon should be a PNG that is 16px x 16px.</span>';
$_['entry_image_category']		= '目錄清單大小：';
$_['entry_image_thumb']			= '產品縮圖的大小：';
$_['entry_image_popup']			= '產品快顯的圖檔大小：';
$_['entry_image_product']		= '產品圖檔清單大小：';
$_['entry_image_additional']	= '相它商品的圖檔大小：';
$_['entry_image_related']		= '相關商品的圖檔大小：';
$_['entry_image_compare']		= '比較的圖檔大小：';
$_['entry_image_wishlist']		= '喜好清單的圖檔大小：';
$_['entry_image_cart']			= '購物車的圖片大小：';
$_['entry_mail_protocol']		= 'Mail 通訊協定：<span class="help">Only choose \'Mail\' unless your host has disabled the php mail function.';
$_['entry_mail_parameter']		= 'Mail 參數：<span class="help">When using \'Mail\', additional mail parameters can be added here (e.g. "-femail@storeaddress.com".';
$_['entry_smtp_host']			= 'SMTP 主機：';
$_['entry_smtp_username']		= 'SMTP 使用者名稱：';
$_['entry_smtp_password']		= 'SMTP 密碼：';
$_['entry_smtp_port']			= 'SMTP 通訊埠：';
$_['entry_smtp_timeout']		= 'SMTP 逾時：';
$_['entry_account_mail']		= 'New Account Alert Mail:<br /><span class="help">Send a email to the store owner when a new account is registered.</span>';
$_['entry_alert_mail']			= 'New Order Alert Mail:<br /><span class="help">Send a email to the store owner when a new order is created.</span>';
$_['entry_alert_emails']		= '其他通知的電子郵件：<br /><span class="help">Any additional emails you want to receive the alert email, in addition to the main store email. (逗號分隔)</span>';
$_['entry_use_ssl']				= '使用 SSL：<br /><span class="help">To use SSL check with your host if a SSL certificate is installed and added the SSL URL to the catalog and admin config files.</span>';
$_['entry_seo_url']				= '使用 SEO URL\'s：<br /><span class="help">To use SEO URL\'s apache module mod-rewrite must be installed and you need to rename the htaccess.txt to .htaccess.</span>';
$_['entry_maintenance']			= '維護模式：<br /><span class="help">Prevents customers from browsing your store. They will instead see a maintenance message. If logged in as admin, you will see the store as normal.</span>';
$_['entry_encryption']			= '加密金鑰：<br /><span class="help">Please provide a secret key that will be used to encrypt private information when processing orders.</span>';
$_['entry_compression']			= 'Output Compression Level:<br /><span class="help">GZIP for more efficient transfer to requesting clients. Compression level must be between 0 - 9</span>';
$_['entry_error_display']		= '顯示錯誤：';
$_['entry_error_log']			= '錯誤記錄：';
$_['entry_error_filename']		= '錯誤記錄檔名：';
$_['entry_google_analytics']	= 'Google Analytics 代碼：<br /><span class="help">Login to your <a onclick="window.open(\'http://www.google.com/analytics/\');"><u>Google Analytics</u></a> account and after creating your web site profile copy and paste the analytics code into this field.</span>';

// Error
$_['error_warning']				= '警告：請仔細檢查表格的錯誤！';
$_['error_permission']			= '警告：您沒有修改商店的權限！';
$_['error_name']				= '商店名稱的長度必須介於 3 和 32 個字元之間！';
$_['error_owner']				= '商店店長的長度必須介於 3 和 64 個字元之間！';
$_['error_address']				= '商店店長的長度必須介於 10 和 256 個字元之間！';
$_['error_email']				= '電子郵件地址可能無效！';
$_['error_telephone']			= '電話號碼的長度必須介於 3 和 32 個字元之間！';
$_['error_title']				= '標題的長度必須介於 3 和 32 個字元之間！';
$_['error_limit']				= '所需的限制！';
$_['error_image_thumb']			= 'Product Image Thumb Size dimensions required！';
$_['error_image_popup']			= 'Product Image Popup Size dimensions required！';
$_['error_image_product']		= 'Product List Size dimensions required！';
$_['error_image_category']		= 'Category List Size dimensions required！';
$_['error_image_manufacturer']	= 'Manufacturer List Size dimensions required！';
$_['error_image_additional']	= 'Additional Product Image Size dimensions required！';
$_['error_image_related']		= 'Related Product Image Size dimensions required！';
$_['error_image_compare']		= 'Compare Image Size dimensions required！';
$_['error_image_wishlist']		= 'Wish List Image Size dimensions required！';
$_['error_image_cart']			= 'Cart Image Size dimensions required！';
$_['error_error_filename']		= 'Error Log Filename required！';
?>