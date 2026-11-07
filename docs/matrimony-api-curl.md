# Matrimony profile API cURL examples

Replace `YOUR_TOKEN` and the local file paths. Both requests use `multipart/form-data` because photos and the caste certificate are files.

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
