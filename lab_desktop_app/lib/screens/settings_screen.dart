import 'package:flutter/material.dart';
import 'package:lab_desktop_app/services/api_service.dart';

class SettingsScreen extends StatefulWidget {
  const SettingsScreen({super.key});

  @override
  State<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen> {
  final ApiService _apiService = ApiService();
  bool _isLoading = true;
  bool _isSaving = false;
  Map<String, TextEditingController> _controllers = {};

  @override
  void initState() {
    super.initState();
    _fetchSettings();
  }

  Future<void> _fetchSettings() async {
    setState(() => _isLoading = true);
    final result = await _apiService.get('/admin/settings');
    if (result['success'] == true) {
      final List data = result['data'];
      _controllers = {
        for (var item in data)
          item['key']: TextEditingController(text: item['value'] ?? '')
      };
      setState(() => _isLoading = false);
    }
  }

  Future<void> _saveSettings() async {
    setState(() => _isSaving = true);
    final body = {
      for (var entry in _controllers.entries) entry.key: entry.value.text
    };
    final result = await _apiService.put('/admin/settings', body);
    setState(() => _isSaving = false);

    if (result['success'] == true) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Settings saved successfully!'), backgroundColor: Colors.green));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(32.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Global Settings',
            style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white),
          ),
          const Text(
            'Configure your laboratory profile and system defaults.',
            style: TextStyle(color: Colors.grey),
          ),
          const SizedBox(height: 32),
          if (_isLoading)
            const Expanded(child: Center(child: CircularProgressIndicator(color: Color(0xFFC9A227))))
          else
            Expanded(
              child: Container(
                padding: const EdgeInsets.all(32),
                decoration: BoxDecoration(
                  color: const Color(0xFF1A1D27),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: ListView(
                  children: [
                    _buildSectionHeader('Lab Identity'),
                    _buildTextField('lab_name', 'Business Display Name'),
                    const SizedBox(height: 24),
                    _buildSectionHeader('Contact Information'),
                    _buildTextField('contact_phone', 'Public Phone'),
                    const SizedBox(height: 16),
                    _buildTextField('contact_email', 'Public Email'),
                    const SizedBox(height: 16),
                    _buildTextField('address', 'Full Lab Address', maxLines: 2),
                    const SizedBox(height: 24),
                    _buildSectionHeader('Finance & Taxes'),
                    Row(
                      children: [
                        Expanded(child: _buildTextField('tax_rate', 'GST / Tax Rate (%)')),
                        const SizedBox(width: 16),
                        Expanded(child: _buildTextField('currency_symbol', 'Currency Symbol')),
                      ],
                    ),
                    const SizedBox(height: 48),
                    SizedBox(
                      height: 56,
                      child: ElevatedButton(
                        onPressed: _isSaving ? null : _saveSettings,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFFC9A227),
                          foregroundColor: Colors.black,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                        child: _isSaving 
                          ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.black)) 
                          : const Text('SAVE CONFIGURATION', style: TextStyle(fontWeight: FontWeight.bold)),
                      ),
                    ),
                  ],
                ),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Text(title.toUpperCase(), style: const TextStyle(color: Color(0xFFC9A227), fontSize: 12, fontWeight: FontWeight.bold, letterSpacing: 1.2)),
    );
  }

  Widget _buildTextField(String key, String label, {int maxLines = 1}) {
    return TextFormField(
      controller: _controllers[key],
      maxLines: maxLines,
      style: const TextStyle(color: Colors.white),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(color: Colors.grey),
        filled: true,
        fillColor: Colors.white.withOpacity(0.02),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.white.withOpacity(0.1))),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.white.withOpacity(0.1))),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: Color(0xFFC9A227))),
      ),
    );
  }
}
