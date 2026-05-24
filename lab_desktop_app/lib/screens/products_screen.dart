import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import 'package:lab_desktop_app/providers/catalog_provider.dart';
import 'package:lab_desktop_app/models/product_model.dart';
import 'package:lab_desktop_app/widgets/product_dialog.dart';

class ProductsScreen extends StatefulWidget {
  const ProductsScreen({super.key});

  @override
  State<ProductsScreen> createState() => _ProductsScreenState();
}

class _ProductsScreenState extends State<ProductsScreen> {
  final NumberFormat _currencyFormat = NumberFormat.currency(locale: 'en_IN', symbol: '₹');

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<CatalogProvider>().fetchProducts();
    });
  }

  @override
  Widget build(BuildContext context) {
    final provider = context.watch<CatalogProvider>();

    return Padding(
      padding: const EdgeInsets.all(32.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Product Catalog',
                    style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white),
                  ),
                  Text(
                    'Manage items, pricing, and availability.',
                    style: TextStyle(color: Colors.grey),
                  ),
                ],
              ),
              ElevatedButton.icon(
                onPressed: () => _openAddProductDialog(context),
                icon: const Icon(Icons.add, size: 18),
                label: const Text('ADD PRODUCT'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFC9A227),
                  foregroundColor: Colors.black,
                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                ),
              ),
            ],
          ),
          const SizedBox(height: 32),
          if (provider.isLoading)
            const Expanded(child: Center(child: CircularProgressIndicator(color: Color(0xFFC9A227))))
          else if (provider.errorMessage != null)
            Center(child: Text(provider.errorMessage!, style: const TextStyle(color: Colors.redAccent)))
          else
            Expanded(
              child: GridView.builder(
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 3,
                  crossAxisSpacing: 24,
                  mainAxisSpacing: 24,
                  childAspectRatio: 2.2,
                ),
                itemCount: provider.products.length,
                itemBuilder: (context, index) {
                  return _buildProductCard(provider.products[index], provider);
                },
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildProductCard(Product product, CatalogProvider provider) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: const Color(0xFF1A1D27),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: Row(
        children: [
          // Icon/Image Placeholder
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: const Color(0xFFC9A227).withOpacity(0.1),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(
              _getCategoryIcon(product.category),
              color: const Color(0xFFC9A227),
              size: 32,
            ),
          ),
          const SizedBox(width: 20),
          // Info
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      product.category.toUpperCase(),
                      style: const TextStyle(color: Colors.grey, fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 1),
                    ),
                    Switch(
                      value: product.active,
                      activeColor: Colors.greenAccent,
                      onChanged: (val) => provider.toggleProductStatus(product.id, product.active),
                    ),
                  ],
                ),
                Text(
                  product.name,
                  style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 4),
                Text(
                  '${_currencyFormat.format(product.price)} / ${product.unit}',
                  style: const TextStyle(color: Color(0xFFC9A227), fontWeight: FontWeight.w600),
                ),
                const Spacer(),
                Row(
                  children: [
                    Text(
                      'Min Qty: ${product.minQty}',
                      style: const TextStyle(color: Colors.white24, fontSize: 11),
                    ),
                    const Spacer(),
                    IconButton(
                      icon: const Icon(Icons.edit_outlined, size: 18, color: Colors.grey),
                      onPressed: () => _openEditProductDialog(context, product),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  IconData _getCategoryIcon(String category) {
    switch (category.toLowerCase()) {
      case 'album': return Icons.menu_book_rounded;
      case 'print': return Icons.photo_library_rounded;
      case 'frame': return Icons.crop_original_rounded;
      case 'gift': return Icons.card_giftcard_rounded;
      default: return Icons.inventory_2_outlined;
    }
  }

  void _openAddProductDialog(BuildContext context) async {
    final result = await showDialog<Map<String, dynamic>>(
      context: context,
      builder: (context) => const ProductDialog(),
    );

    if (result != null && mounted) {
      await context.read<CatalogProvider>().saveProduct(result);
    }
  }

  void _openEditProductDialog(BuildContext context, Product product) async {
    final result = await showDialog<Map<String, dynamic>>(
      context: context,
      builder: (context) => ProductDialog(product: product),
    );

    if (result != null && mounted) {
      await context.read<CatalogProvider>().saveProduct(result, id: product.id);
    }
  }
}
