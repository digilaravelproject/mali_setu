# Matrimony profile API cURL examples

Replace `YOUR_TOKEN` and the local file paths. Both requests use `multipart/form-data` because photos and the caste certificate are files.

## Login and get the bearer token

The authenticated matrimony APIs below require the value returned in `data.token`.

```bash
curl --location 'https://malisetu.com/api/auth/login' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data-raw '{
    "email": "USER_EMAIL",
    "password": "USER_PASSWORD"
  }'
```

## Register matrimony profile

```bash
curl --location 'https://malisetu.com/api/matrimony/profile' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer YOUR_TOKEN' \
  --form 'age=29' \
  --form 'height=5.6' \
  --form 'weight=58' \
  --form 'complexion=Fair' \
  --form 'physical_status=Normal' \
  --form 'personal_details[first_name]=Asha' \
  --form 'personal_details[middle_name]=Ramesh' \
  --form 'personal_details[last_name]=Mali' \
  --form 'personal_details[name]=Asha Ramesh Mali' \
  --form 'personal_details[gender]=Female' \
  --form 'personal_details[dob]=1997-05-14' \
  --form 'personal_details[marital_status]=Never Married' \
  --form 'personal_details[mother_tongue]=Marathi' \
  --form 'personal_details[caste]=Mali' \
  --form 'personal_details[sub_caste]=Phulmali' \
  --form 'personal_details[profile_created_by]=Self' \
  --form 'family_details[father_name]=Ramesh Mali' \
  --form 'family_details[father_occupation]=Farmer' \
  --form 'family_details[mother_name]=Sunita Mali' \
  --form 'family_details[mother_occupation]=Homemaker' \
  --form 'family_details[family_type]=Joint' \
  --form 'family_details[family_class]=Middle Class' \
  --form 'family_details[family_value]=Traditional' \
  --form 'education_details[highest_qualification]=B.E.' \
  --form 'education_details[college_name]=Pune University' \
  --form 'education_details[passing_year]=2019' \
  --form 'professional_details[occupation]=Software Engineer' \
  --form 'professional_details[employment_type]=Private' \
  --form 'professional_details[company_name]=Example Technologies' \
  --form 'professional_details[annual_income]=800000' \
  --form 'lifestyle_details[diet]=Vegetarian' \
  --form 'lifestyle_details[smoking]=No' \
  --form 'lifestyle_details[drinking]=No' \
  --form 'location_details[address]=Kothrud' \
  --form 'location_details[country]=India' \
  --form 'location_details[state]=Maharashtra' \
  --form 'location_details[district]=Pune' \
  --form 'location_details[taluka]=Haveli' \
  --form 'location_details[city]=Pune' \
  --form 'location_details[pincode]=411038' \
  --form 'partner_preferences[age_min]=28' \
  --form 'partner_preferences[age_max]=34' \
  --form 'partner_preferences[religion]=Hindu' \
  --form 'partner_preferences[caste]=Mali' \
  --form 'partner_preferences[education]=Graduate' \
  --form 'partner_preferences[location]=Maharashtra' \
  --form 'privacy_settings[show_phone]=0' \
  --form 'privacy_settings[show_email]=0' \
  --form 'photos[]=@C:/files/profile-1.jpg' \
  --form 'photos[]=@C:/files/profile-2.jpg' \
  --form 'cast_certificate=@C:/files/caste-certificate.pdf'
```

Registration requires at least two photos. `cast_certificate` accepts PDF, JPEG, JPG, or PNG up to 5 MB.

## Update authenticated user's matrimony profile

Use a real POST request with `_method=PUT`. This lets PHP parse multipart files while Laravel matches the existing `PUT /api/matrimony/profile` route.

```bash
curl --location 'https://malisetu.com/api/matrimony/profile' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer YOUR_TOKEN' \
  --form '_method=PUT' \
  --form 'age=30' \
  --form 'height=5.6' \
  --form 'weight=59' \
  --form 'complexion=Fair' \
  --form 'physical_status=Normal' \
  --form 'personal_details[first_name]=Asha' \
  --form 'personal_details[middle_name]=Ramesh' \
  --form 'personal_details[last_name]=Mali' \
  --form 'personal_details[name]=Asha Ramesh Mali' \
  --form 'personal_details[gender]=Female' \
  --form 'personal_details[dob]=1997-05-14' \
  --form 'personal_details[marital_status]=Never Married' \
  --form 'personal_details[mother_tongue]=Marathi' \
  --form 'personal_details[caste]=Mali' \
  --form 'personal_details[sub_caste]=Phulmali' \
  --form 'family_details[father_name]=Ramesh Mali' \
  --form 'family_details[father_occupation]=Farmer' \
  --form 'family_details[mother_name]=Sunita Mali' \
  --form 'family_details[mother_occupation]=Homemaker' \
  --form 'family_details[family_type]=Joint' \
  --form 'education_details[highest_qualification]=B.E.' \
  --form 'education_details[college_name]=Pune University' \
  --form 'professional_details[occupation]=Senior Software Engineer' \
  --form 'professional_details[employment_type]=Private' \
  --form 'professional_details[company_name]=Example Technologies' \
  --form 'professional_details[annual_income]=1000000' \
  --form 'lifestyle_details[diet]=Vegetarian' \
  --form 'lifestyle_details[smoking]=No' \
  --form 'lifestyle_details[drinking]=No' \
  --form 'location_details[address]=Kothrud' \
  --form 'location_details[country]=India' \
  --form 'location_details[state]=Maharashtra' \
  --form 'location_details[district]=Pune' \
  --form 'location_details[taluka]=Haveli' \
  --form 'location_details[city]=Pune' \
  --form 'location_details[pincode]=411038' \
  --form 'partner_preferences[age_min]=28' \
  --form 'partner_preferences[age_max]=35' \
  --form 'partner_preferences[religion]=Hindu' \
  --form 'partner_preferences[caste]=Mali' \
  --form 'privacy_settings[show_phone]=0' \
  --form 'privacy_settings[show_email]=0' \
  --form 'photos[]=@C:/files/new-profile-1.jpg' \
  --form 'photos[]=@C:/files/new-profile-2.jpg' \
  --form 'cast_certificate=@C:/files/new-caste-certificate.pdf'
```

Every update field is optional. Omit `photos[]` to retain existing photos. Omit `cast_certificate` to retain the existing certificate. A replacement certificate deletes the previous file and resets verification to `pending`.

## Report a matrimony user

Replace `REPORTED_USER_ID` with the target user's ID (not the matrimony profile ID). Supported application reason keys include `fake_profile`, `inappropriate_content`, `harassment`, `spam`, `fraud_scam`, and `other`. The API accepts any stable reason key up to 100 characters so new reasons can be added without an app release.

```bash
curl --location 'https://malisetu.com/api/matrimony/report-user' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Bearer YOUR_TOKEN' \
  --data '{
    "reported_user_id": REPORTED_USER_ID,
    "reason_key": "fake_profile",
    "details": "The profile information appears to use a false identity."
  }'
```

After a successful report, the reported profile is excluded from this reporter's matrimony search/listing and direct profile endpoints. Repeating the request updates the reason/details instead of creating a duplicate report.

## Block or unblock a matrimony user (toggle)

Replace `BLOCKED_USER_ID` with the target user's ID. Call the same endpoint once to block and again to unblock. Check `data.is_blocked` in the response to know the resulting state.

```bash
curl --location 'https://malisetu.com/api/matrimony/block-user' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Bearer YOUR_TOKEN' \
  --data '{
    "blocked_user_id": BLOCKED_USER_ID
  }'
```

When blocked, the target stays in the matrimony listing with `connection_status: "blocked"` and `is_blocked: true`. Connection requests are disabled while either user has blocked the other.

## Verify listing status

This is the listing endpoint currently used by the mobile search flow. A reported profile is absent; a blocked profile remains present with the blocked status.

```bash
curl --location 'https://malisetu.com/api/search/matrimony' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Bearer YOUR_TOKEN' \
  --data '{
    "created_at": "all"
  }'
```
