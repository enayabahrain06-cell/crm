<?php

namespace App\Enums;

enum CustomerType: string
{
    case INDIVIDUAL = 'individual';
    case CORPORATE = 'corporate';
}

enum CustomerStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BLACKLISTED = 'blacklisted';
}

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';
    case UNKNOWN = 'unknown';
}

enum VisitType: string
{
    case STAY = 'stay';
    case DINE = 'dine';
    case BAR = 'bar';
    case EVENT = 'event';
    case OTHER = 'other';
}

enum LoyaltyRuleType: string
{
    case EARN = 'earn';
    case BURN = 'burn';
}

enum RewardType: string
{
    case STAY = 'stay';
    case DRINK = 'drink';
    case VOUCHER = 'voucher';
    case DISCOUNT = 'discount';
    case GIFT = 'gift';
    case OTHER = 'other';
}

enum CampaignStatus: string
{
    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case SENDING = 'sending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}

enum CampaignChannel: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case PUSH = 'push';
}

enum CampaignMessageStatus: string
{
    case QUEUED = 'queued';
    case SENDING = 'sending';
    case SENT = 'sent';
    case FAILED = 'failed';
    case OPENED = 'opened';
    case CLICKED = 'clicked';
}

enum GreetingTriggerType: string
{
    case BIRTHDAY = 'birthday';
    case FIXED_DATE = 'fixed_date';
}

enum GreetingStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';
}

enum OutletType: string
{
    case HOTEL = 'hotel';
    case RESORT = 'resort';
    case BAR = 'bar';
    case RESTAURANT = 'restaurant';
    case CLUB = 'club';
}

enum SocialPlatform: string
{
    case INSTAGRAM = 'instagram';
    case FACEBOOK = 'facebook';
    case TIKTOK = 'tiktok';
    case SNAPCHAT = 'snapchat';
    case WHATSAPP = 'whatsapp';
    case WEBSITE = 'website';
    case EMAIL = 'email';
    case OTHER = 'other';
}

enum LedgerSourceType: string
{
    case VISIT = 'visit';
    case MANUAL_ADJUSTMENT = 'manual_adjustment';
    case CAMPAIGN = 'campaign';
    case EXPIRY = 'expiry';
    case CORRECTION = 'correction';
    case REWARD_REDEMPTION = 'reward_redemption';
}

enum RedemptionStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
}

