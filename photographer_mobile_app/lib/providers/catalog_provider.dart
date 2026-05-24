import 'package:flutter/foundation.dart';
import 'package:photographer_mobile_app/services/api_service.dart';
import 'package:photographer_mobile_app/models/product_model.dart';

class CatalogProvider with ChangeNotifier {
  List<Product> _products = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<Product> get products => _products;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  final ApiService _apiService = ApiService();

  Future<void> fetchProducts({String? category}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final query = category != null ? '?category=$category' : '';
    final result = await _apiService.get('/products$query');

    if (result['success'] == true) {
      _products = (result['data'] as List).map((p) => Product.fromJson(p)).toList();
    } else {
      _errorMessage = result['message'];
    }

    _isLoading = false;
    notifyListeners();
  }
}
