import 'package:flutter/foundation.dart';
import 'package:photographer_mobile_app/services/api_service.dart';
import 'package:photographer_mobile_app/models/order_model.dart';

class OrderProvider with ChangeNotifier {
  List<LabOrder> _orders = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<LabOrder> get orders => _orders;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  final ApiService _apiService = ApiService();

  Future<void> fetchOrders() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final result = await _apiService.get('/orders');

    if (result['success'] == true) {
      _orders = (result['data'] as List).map((o) => LabOrder.fromJson(o)).toList();
    } else {
      _errorMessage = result['message'];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<bool> placeOrder(Map<String, dynamic> orderData) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final result = await _apiService.post('/orders', orderData);

    if (result['success'] == true) {
      await fetchOrders();
      _isLoading = false;
      notifyListeners();
      return true;
    } else {
      _errorMessage = result['message'];
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }
}
