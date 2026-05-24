import 'package:flutter/foundation.dart';
import 'package:lab_desktop_app/services/api_service.dart';
import 'package:lab_desktop_app/models/order_model.dart';

class OrderProvider with ChangeNotifier {
  List<LabOrder> _orders = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<LabOrder> get orders => _orders;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  final ApiService _apiService = ApiService();

  Future<void> fetchOrders({String? status}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final query = status != null ? '?status=$status' : '';
    final result = await _apiService.get('/admin/orders$query');

    if (result['success'] == true) {
      _orders = (result['data'] as List).map((o) => LabOrder.fromJson(o)).toList();
    } else {
      _errorMessage = result['message'];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<bool> updateOrderStatus(int orderId, String status) async {
    final result = await _apiService.patch('/admin/orders/$orderId', {
      'status': status,
    });

    if (result['success'] == true) {
      // Update local state if needed or just refetch
      final index = _orders.indexWhere((o) => o.id == orderId);
      if (index != -1) {
        // We can't easily mutate the final status, so we refetch or replace
        // For simplicity in a list view, refetching or just updating the specific list item is fine
        await fetchOrders(); 
      }
      return true;
    } else {
      _errorMessage = result['message'];
      notifyListeners();
      return false;
    }
  }

  Future<LabOrder?> getOrderDetail(int id) async {
    final result = await _apiService.get('/admin/orders/$id');
    if (result['success'] == true) {
      return LabOrder.fromJson(result['data']);
    }
    return null;
  }
}
