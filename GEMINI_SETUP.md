# 🤖 Google Gemini Integration Setup

Google Gemini has been added as a third AI provider alongside OpenAI and Claude, offering the most cost-effective option for AI website generation.

## 📊 Cost Comparison

| Provider | Input (per 1K tokens) | Output (per 1K tokens) | Typical Website Cost |
|----------|----------------------|------------------------|---------------------|
| **Gemini 1.5 Pro** | $0.00125 | $0.005 | ~$0.010 (cheapest) |
| **Claude 3.5 Sonnet** | $0.003 | $0.015 | ~$0.030 |
| **OpenAI GPT-4** | $0.03 | $0.06 | ~$0.120 (most expensive) |

## 🔧 Setup Instructions

### 1. Get Google Gemini API Key

1. Go to [Google AI Studio](https://aistudio.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy your API key

### 2. Add to Environment Configuration

Add these variables to your `.env` file:

```env
# Google Gemini Configuration
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-1.5-pro
GEMINI_MAX_TOKENS=4000
GEMINI_TEMPERATURE=0.7
```

### 3. Clear Configuration Cache

```bash
php artisan config:clear
```

## 🚀 Provider Fallback Order

The system automatically tries providers in order of cost-effectiveness:

1. **Claude AI** (if configured) - Good balance of cost and quality
2. **OpenAI** (if configured) - Most reliable but expensive
3. **Gemini** (if configured) - Cheapest option
4. **Mock Generation** (fallback) - Ensures system always works

## ✅ Verify Setup

Test your Gemini configuration:

```bash
php artisan tinker --execute="
\$geminiService = app(App\Services\GeminiAIService::class);
echo 'Gemini configured: ' . (\$geminiService->isConfigured() ? 'Yes' : 'No') . PHP_EOL;
echo 'Model: ' . \$geminiService->getModel() . PHP_EOL;
"
```

## 🧪 Testing

Run the real AI tests to verify Gemini integration:

```bash
# Test all AI providers (costs money!)
php artisan test tests/Feature/RealAITest.php

# Test only Gemini (if API key is configured)
php artisan test tests/Feature/RealAITest.php --filter="can generate a real website with Gemini"
```

## 🔍 Troubleshooting

### Gemini Not Working?

1. **Check API Key**: Ensure `GEMINI_API_KEY` is set in `.env`
2. **Verify API Key**: Test the key at [Google AI Studio](https://aistudio.google.com/)
3. **Check Quotas**: Ensure you haven't exceeded API quotas
4. **Clear Cache**: Run `php artisan config:clear`

### Common Issues

- **403 Forbidden**: API key is invalid or doesn't have proper permissions
- **429 Rate Limited**: You've exceeded the API rate limits
- **400 Bad Request**: Check the request format or model name

## 💡 Benefits of Using Gemini

- **Cost-Effective**: Up to 12x cheaper than OpenAI GPT-4
- **High Quality**: Competitive performance with other models
- **Reliable**: Google's enterprise-grade infrastructure
- **Fast**: Quick response times for website generation

## 🔄 How It Works

1. User submits a website generation prompt
2. System tries Claude first (best cost/quality balance)
3. If Claude fails, tries OpenAI (most reliable)
4. If OpenAI fails, tries Gemini (cheapest option)
5. If all fail, uses mock generation (guaranteed to work)

This ensures you always get a website generated while optimizing for cost and reliability!
