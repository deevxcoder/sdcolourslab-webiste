import 'package:photographer_mobile_app/models/product_model.dart';

class CartItem {
  final Product product;
  final String size;
  int quantity;
  String? notes;

  CartItem({
    required this.product,
    required this.size,
    this.quantity = 1,
    this.notes,
  });

  double get subtotal => product.price * quantity;
}
