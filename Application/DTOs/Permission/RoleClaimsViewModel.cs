﻿namespace Application.DTOs.Permission
{
    public class RoleClaimsViewModel
    {
        public int Id { get; set; }
        public string Type { get; set; }
        public string Value { get; set; }
        public string Resources { get; set; }
        public bool IsGranted { get; set; }
    }
}
