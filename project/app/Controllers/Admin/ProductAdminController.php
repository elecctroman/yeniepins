<?php

namespace App\Controllers\Admin;

use App\Models\AccountStock;
use App\Models\Category;
use App\Models\EpinKey;
use App\Models\Product;
use App\Models\Variant;
use Core\CSRF;
use Core\Controller;
use Core\Logger;
use Core\Response;
use Core\Session;

class ProductAdminController extends Controller
{
    protected Product $products;
    protected Category $categories;
    protected Variant $variants;
    protected EpinKey $epinKeys;
    protected AccountStock $accountStock;

    public function __construct()
    {
        parent::__construct();
        $this->products = new Product();
        $this->categories = new Category();
        $this->variants = new Variant();
        $this->epinKeys = new EpinKey();
        $this->accountStock = new AccountStock();
    }

    public function index(array $params = []): void
    {
        $products = $this->products->getCatalog(null, 0, null);
        $summaries = [];
        foreach ($products as $product) {
            $summaries[$product['id']] = $this->products->getStockSummary((int)$product['id']);
        }

        $this->render('admin/products/index', [
            'title' => 'Ürün Yönetimi',
            'topbarTitle' => 'Ürünler',
            'products' => $products,
            'stockSummaries' => $summaries,
        ]);
    }

    public function create(array $params = []): void
    {
        $categories = $this->categories->getVisibleCategories(false);
        $this->render('admin/products/create', [
            'title' => 'Yeni Ürün Oluştur',
            'topbarTitle' => 'Yeni Ürün',
            'categories' => $categories,
        ]);
    }

    public function store(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz istek.');
            Response::redirect('/admin/urunler');
        }

        $input = $this->input();
        [$data, $errors] = $this->validateProductInput($input);
        if ($errors) {
            Session::flash('error', implode('<br>', $errors));
            Session::pushOldInput($input);
            Response::redirect('/admin/urunler/olustur');
        }

        try {
            $productId = $this->products->create($data);
            if (!empty($input['variants']) && is_array($input['variants'])) {
                $this->variants->sync($productId, array_values($input['variants']));
            }
            Session::flash('success', 'Ürün başarıyla oluşturuldu.');
            Logger::info('Yeni ürün oluşturuldu', [
                'product_id' => $productId,
                'admin_id' => Session::get('user_id'),
            ]);
        } catch (\Throwable $e) {
            Logger::error('Ürün oluşturma hatası: {message}', ['message' => $e->getMessage()]);
            Session::flash('error', 'Ürün oluşturulurken hata meydana geldi: ' . strip_tags($e->getMessage()));
            Session::pushOldInput($input);
            Response::redirect('/admin/urunler/olustur');
        }

        Response::redirect('/admin/urunler');
    }

    public function edit(array $params = []): void
    {
        $id = (int)($params['id'] ?? 0);
        $product = $this->products->findById($id);
        if (!$product) {
            Session::flash('error', 'Ürün bulunamadı.');
            Response::redirect('/admin/urunler');
        }

        $categories = $this->categories->getVisibleCategories(false);
        $this->render('admin/products/edit', [
            'title' => 'Ürün Düzenle',
            'topbarTitle' => 'Ürün Düzenle',
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz istek.');
            Response::redirect('/admin/urunler');
        }

        $id = (int)($params['id'] ?? 0);
        $product = $this->products->findById($id);
        if (!$product) {
            Session::flash('error', 'Ürün bulunamadı.');
            Response::redirect('/admin/urunler');
        }

        $input = $this->input();
        [$data, $errors] = $this->validateProductInput($input, $product);
        if ($errors) {
            Session::flash('error', implode('<br>', $errors));
            Session::pushOldInput($input);
            Response::redirect('/admin/urunler/' . $id . '/duzenle');
        }

        try {
            $this->products->updateProduct($id, $data);
            if (!empty($input['variants']) && is_array($input['variants'])) {
                $this->variants->sync($id, array_values($input['variants']));
            } else {
                $this->variants->sync($id, []);
            }
            Session::flash('success', 'Ürün güncellendi.');
            Logger::info('Ürün güncellendi', [
                'product_id' => $id,
                'admin_id' => Session::get('user_id'),
            ]);
        } catch (\Throwable $e) {
            Logger::error('Ürün güncelleme hatası: {message}', ['message' => $e->getMessage(), 'product_id' => $id]);
            Session::flash('error', 'Ürün güncellenemedi: ' . strip_tags($e->getMessage()));
            Session::pushOldInput($input);
            Response::redirect('/admin/urunler/' . $id . '/duzenle');
        }

        Response::redirect('/admin/urunler');
    }

    public function destroy(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz istek.');
            Response::redirect('/admin/urunler');
        }

        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            Session::flash('error', 'Geçersiz ürün.');
            Response::redirect('/admin/urunler');
        }

        $this->products->delete($id);
        Session::flash('success', 'Ürün silindi.');
        Logger::warning('Ürün silindi', [
            'product_id' => $id,
            'admin_id' => Session::get('user_id'),
        ]);
        Response::redirect('/admin/urunler');
    }

    public function stock(array $params = []): void
    {
        $products = $this->products->getCatalog(null, 0, null);
        $stockCards = [];
        $epinLists = [];
        $accountLists = [];

        foreach ($products as $product) {
            $summary = $this->products->getStockSummary((int)$product['id']);
            $stockCards[] = [
                'product' => $product,
                'summary' => $summary,
            ];

            if ($product['type'] === 'epin') {
                $epinLists[$product['id']] = $this->epinKeys->listForProduct((int)$product['id']);
            }
            if ($product['type'] === 'account') {
                $accountLists[$product['id']] = $this->accountStock->listForProduct((int)$product['id']);
            }
        }

        $this->render('admin/stock', [
            'title' => 'Stok Yönetimi',
            'topbarTitle' => 'Stoklar',
            'stockCards' => $stockCards,
            'epinLists' => $epinLists,
            'accountLists' => $accountLists,
        ]);
    }

    public function uploadEpinCsv(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz istek.');
            Response::redirect('/admin/stok');
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $product = $this->products->findById($productId);
        if (!$product || $product['type'] !== 'epin') {
            Session::flash('error', 'E-PIN ürün seçilmelidir.');
            Response::redirect('/admin/stok');
        }

        if (!isset($_FILES['csv_file']) || !is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
            Session::flash('error', 'CSV dosyası yüklenemedi.');
            Response::redirect('/admin/stok');
        }

        $file = $_FILES['csv_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Dosya yükleme hatası oluştu.');
            Response::redirect('/admin/stok');
        }

        $allowedMime = ['text/csv', 'text/plain', 'application/vnd.ms-excel'];
        $mime = mime_content_type($file['tmp_name']);
        if ($mime && !in_array($mime, $allowedMime, true)) {
            Session::flash('error', 'Geçersiz dosya formatı.');
            Response::redirect('/admin/stok');
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            Session::flash('error', 'Dosya boyutu 2MB sınırını aşıyor.');
            Response::redirect('/admin/stok');
        }

        $uploadDir = __DIR__ . '/../../storage/uploads/csv/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $safeName = 'epin_' . $productId . '_' . time() . '.csv';
        $target = $uploadDir . $safeName;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            Session::flash('error', 'Dosya taşınamadı.');
            Response::redirect('/admin/stok');
        }

        $codes = $this->readCsvCodes($target);
        if (!$codes) {
            Session::flash('error', 'Geçerli E-PIN kodu bulunamadı.');
            Response::redirect('/admin/stok');
        }

        $batchId = 'BATCH-' . date('YmdHis');
        $result = $this->epinKeys->bulkInsert($productId, $codes, $batchId);
        Session::flash('success', sprintf('E-PIN yüklemesi tamamlandı. %d yeni kayıt, %d atlanan.', $result['inserted'], $result['skipped']));
        Logger::info('E-PIN CSV yüklendi', [
            'product_id' => $productId,
            'batch_id' => $batchId,
            'inserted' => $result['inserted'],
            'skipped' => $result['skipped'],
        ]);
        Response::redirect('/admin/stok');
    }

    public function addAccount(array $params = []): void
    {
        if (!CSRF::validate()) {
            Session::flash('error', 'Geçersiz istek.');
            Response::redirect('/admin/stok');
        }

        $input = $this->input();
        $productId = (int)($input['product_id'] ?? 0);
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');
        $extraRaw = $input['extra'] ?? '';

        $product = $this->products->findById($productId);
        if (!$product || $product['type'] !== 'account') {
            Session::flash('error', 'Hesap tipi ürün seçilmelidir.');
            Response::redirect('/admin/stok');
        }

        if ($username === '' || $password === '') {
            Session::flash('error', 'Kullanıcı adı ve şifre gereklidir.');
            Response::redirect('/admin/stok');
        }

        $extra = [];
        if ($extraRaw !== '') {
            $decoded = json_decode($extraRaw, true);
            if (!is_array($decoded)) {
                Session::flash('error', 'Ek bilgi JSON formatında olmalıdır.');
                Response::redirect('/admin/stok');
            }
            $extra = $decoded;
        }

        try {
            $this->accountStock->addAccount($productId, $username, $password, $extra);
            Session::flash('success', 'Hesap stoğa eklendi.');
            Logger::info('Hesap stoğa eklendi', [
                'product_id' => $productId,
                'admin_id' => Session::get('user_id'),
                'username' => $username,
            ]);
        } catch (\Throwable $e) {
            Logger::error('Hesap stok ekleme hatası: {message}', [
                'message' => $e->getMessage(),
                'product_id' => $productId,
            ]);
            Session::flash('error', 'Hesap eklenemedi: ' . strip_tags($e->getMessage()));
        }

        Response::redirect('/admin/stok');
    }

    protected function validateProductInput(array $input, ?array $existing = null): array
    {
        $errors = [];
        $allowedTypes = ['epin', 'license', 'account'];
        $allowedDeliveries = ['instant', 'manual'];
        $allowedStatuses = ['draft', 'active', 'inactive'];

        $name = trim($input['name'] ?? '');
        if ($name === '') {
            $errors[] = 'Ürün adı gereklidir.';
        }

        $type = $input['type'] ?? '';
        if (!in_array($type, $allowedTypes, true)) {
            $errors[] = 'Geçerli bir ürün tipi seçiniz.';
        }

        $delivery = $input['delivery'] ?? 'instant';
        if (!in_array($delivery, $allowedDeliveries, true)) {
            $delivery = 'instant';
        }

        $status = $input['status'] ?? ($existing['status'] ?? 'draft');
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'draft';
        }

        $price = str_replace(',', '.', (string)($input['price'] ?? '0'));
        $tax = str_replace(',', '.', (string)($input['tax_rate'] ?? '0'));
        $priceValue = round((float)$price, 2);
        $taxValue = max(0, round((float)$tax, 2));

        if ($priceValue < 0) {
            $errors[] = 'Fiyat 0\'dan küçük olamaz.';
        }

        $minQty = max(1, (int)($input['min_qty'] ?? 1));
        $maxQty = (int)($input['max_qty'] ?? 0);
        if ($maxQty > 0 && $maxQty < $minQty) {
            $errors[] = 'Maksimum adet minimum adetten küçük olamaz.';
        }

        $stockPolicy = $input['stock_policy'] === 'track' ? 'track' : 'unlimited';
        if ($type !== 'license') {
            $stockPolicy = 'track';
        }

        $slug = trim($input['slug'] ?? '');
        if ($slug === '') {
            $slug = \str_slug($name);
        }

        $description = $input['description'] ?? '';
        $categoryId = isset($input['category_id']) && $input['category_id'] !== '' ? (int)$input['category_id'] : null;

        $data = [
            'category_id' => $categoryId,
            'type' => $type,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'price' => $priceValue,
            'tax_rate' => $taxValue,
            'stock_policy' => $stockPolicy,
            'delivery' => $delivery,
            'min_qty' => $minQty,
            'max_qty' => $maxQty,
            'status' => $status,
        ];

        return [$data, $errors];
    }

    protected function readCsvCodes(string $path): array
    {
        $codes = [];
        if (!file_exists($path)) {
            return $codes;
        }

        $handle = fopen($path, 'rb');
        if (!$handle) {
            return $codes;
        }

        while (($row = fgetcsv($handle, 1000, ';')) !== false) {
            if (count($row) === 1) {
                $codes[] = trim($row[0]);
                continue;
            }
            if (count($row) > 1) {
                $codes[] = trim($row[0]);
            }
        }
        fclose($handle);

        $codes = array_filter(array_unique(array_map('trim', $codes)), static fn($code) => $code !== '');
        return array_values($codes);
    }
}
