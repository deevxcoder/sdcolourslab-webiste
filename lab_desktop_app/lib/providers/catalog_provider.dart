import 'package:flutter/foundation.dart';
import 'package:lab_desktop_app/services/api_service.dart';
import 'package:lab_desktop_app/models/product_model.dart';

class CatalogProvider with ChangeNotifier {
  List<Product> _products = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<Product> get products => _products;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  final ApiService _apiService = ApiService();

  Future<void> fetchProducts() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final result = await _apiService.get('/admin/products');

    if (result['success'] == true) {
      _products = (result['data'] as List).map((p) => Product.fromJson(p)).toList();
    } else {
      _errorMessage = result['message'];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<bool> saveProduct(Map<String, dynamic> data, {int? id}) async {
    _isLoading = true;
    notifyListeners();

    Map<String, dynamic> result;
    if (id != null) {
      result = await _apiService.patch('/admin/products/$id', data);
    } else {
      result = await _apiService.post('/admin/products', data);
    }

    if (result['success'] == true) {
      await fetchProducts();
      return true;
    } else {
      _errorMessage = result['message'];
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> toggleProductStatus(int id, bool currentStatus) async {
    final result = await _apiService.patch('/admin/products/$id', {
      'active': !currentStatus,
    });

    if (result['success'] == true) {
      await fetchProducts();
      return true;
    }
    return false;
  }
}
